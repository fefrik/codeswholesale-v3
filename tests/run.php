<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'CodesWholesaleApi\\';
    if (strpos($class, $prefix) !== 0) return;
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) require $file;
});

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Auth\TokenNormalizer;
use CodesWholesaleApi\CodesWholesale;
use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Http\HttpResponse;
use CodesWholesaleApi\Enum\CodeType;
use CodesWholesaleApi\Enum\OrderStatus;
use CodesWholesaleApi\Mode;
use CodesWholesaleApi\OAuthStorageMode;
use CodesWholesaleApi\Resource\CodeItem;
use CodesWholesaleApi\Resource\Exceptions\NoImagesFoundException;
use CodesWholesaleApi\Resource\Exceptions\ResourceMappingException;
use CodesWholesaleApi\Resource\OrderDetailItem;
use CodesWholesaleApi\Resource\ProductDescriptionItem;
use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Service\ImageCodeWriter;
use CodesWholesaleApi\Sdk\Sdk;
use CodesWholesaleApi\Api\ProductsApi;
use CodesWholesaleApi\Storage\ContinuationToken\ContinuationTokenStorageInterface;
use CodesWholesaleApi\Storage\OAuth2\OAuthStorageInterface;

final class MemoryOAuthStorage implements OAuthStorageInterface
{
    private ?array $token = null;
    public function saveToken(array $tokenData): void { $this->token = $tokenData; }
    public function getToken(): ?array { return $this->token; }
    public function clearToken(): void { $this->token = null; }
}

final class MemoryContinuationStorage implements ContinuationTokenStorageInterface
{
    public ?string $token = null;
    /** @var array<int, ?string> */
    public array $history = [];
    public function getToken(): ?string { return $this->token; }
    public function saveToken(?string $token): void { $this->history[] = $token; $this->token = $token; }
    public function clearToken(): void { $this->token = null; }
}

final class PagedClient extends Client
{
    /** @var array<string, \stdClass> */
    private array $pages;

    /** @param array<string, \stdClass> $pages */
    public function __construct(array $pages) { $this->pages = $pages; }

    public function requestData(string $method, string $path, ?array $body = null, array $query = []): \stdClass
    {
        $token = (string) ($query['continuationToken'] ?? '');
        return $this->pages[$token];
    }
}

function expect(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

function expectException(callable $callback, string $class): void
{
    try { $callback(); } catch (Throwable $e) {
        expect($e instanceof $class, 'Expected ' . $class . ', got ' . get_class($e));
        return;
    }
    throw new RuntimeException('Expected exception ' . $class);
}

$token = (new TokenNormalizer(60))->normalize([
    'access_token' => 'secret', 'token_type' => 'bearer', 'expires_in' => 3600,
]);
expect($token['access_token'] === 'secret', 'OAuth token normalization failed.');
expect($token['expires_at'] >= time() + 3500, 'OAuth expiry buffer is incorrect.');

$storage = new MemoryOAuthStorage();
$client = new Client(Config::sandbox(), $storage, 'client', 'secret');
expect(CodesWholesale::sdk($client) instanceof Sdk, 'Public SDK facade must return Sdk.');
expect(Mode::LIVE === 'live' && OAuthStorageMode::SESSION === 'session', 'PSR-4 mode classes are not autoloadable.');
expectException(static function () use ($storage): void { new Client(Config::sandbox(), $storage, '', 'secret'); }, InvalidArgumentException::class);
expectException(static function () use ($storage): void { new Client(Config::sandbox(), $storage, 'client', 'secret', null, 0); }, InvalidArgumentException::class);
expectException(static function () use ($storage): void { new Client(Config::sandbox(), $storage, 'client', 'secret', null, 20, "ok\r\nInjected: yes"); }, InvalidArgumentException::class);

$arrayResponse = new HttpResponse(200, '[]', []);
expect(is_array($arrayResponse->getJsonBody()), 'HttpResponse must safely represent top-level JSON arrays.');

$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cw-sdk-' . bin2hex(random_bytes(6));
$code = new CodeItem((object) ['codeType' => 'CODE_IMAGE', 'code' => base64_encode('image-data'), 'filename' => '../key.png']);
$path = (new ImageCodeWriter())->write($code, $tempDir);
expect(basename($path) === 'key.png', 'Image filename traversal was not removed.');
expect(file_get_contents($path) === 'image-data', 'Image code content was not saved correctly.');
expect($code->getType() === CodeType::Image, 'Code type enum mapping failed.');
expectException(static function () use ($code, $tempDir): void { (new ImageCodeWriter())->write($code, $tempDir); }, RuntimeException::class);
unlink($path);
rmdir($tempDir);

$source = (object) [
    'productId' => 'immutable',
    'name' => 'Original',
    'quantity' => 5,
    'releaseDate' => '2026-08-01T10:15:00+02:00',
    'regions' => ['WORLDWIDE'],
    'images' => [(object) ['format' => 'COVER', 'image' => null]],
    'prices' => [(object) ['from' => 1, 'value' => 9.99]],
];
$resource = new ProductItem($source);
$source->name = 'Changed outside';
$raw = $resource->raw();
$raw->name = 'Changed raw copy';
expect($resource->getName() === 'Original', 'Resource data must be immutable from input and raw copies.');
expect($resource->getReleaseDate()?->format(DATE_ATOM) === '2026-08-01T10:15:00+02:00', 'Release date mapping lost timezone information.');
expect($resource->getRegions() === ['WORLDWIDE'], 'Typed string list mapping failed.');
expect(count(iterator_to_array($resource->iteratePrices())) === 1, 'Nested price iterator failed.');
expectException(static function () use ($resource): void { $resource->getImageUrl('COVER'); }, NoImagesFoundException::class);
expectException(static function (): void { (new ProductItem((object) ['quantity' => '5']))->getStock(); }, ResourceMappingException::class);

$description = new ProductDescriptionItem((object) [
    'eans' => ['1234567890123'],
    'localizedTitles' => [(object) ['territory' => 'CZ', 'title' => 'Český název']],
    'factSheets' => [(object) ['territory' => 'CZ', 'description' => 'Popis']],
    'photos' => [(object) ['territory' => 'CZ', 'type' => 'COVER', 'url' => 'https://example.test/cover.jpg']],
    'videos' => [(object) ['ageWarning' => false, 'title' => 'Trailer', 'url' => 'https://example.test/video']],
    'releases' => [(object) ['releaseDate' => '2026-08-01', 'releaseStatus' => 'RELEASED', 'territory' => 'CZ']],
]);
expect($description->getEans() === ['1234567890123'], 'Description scalar lists are not typed.');
expect($description->getLocalizedTitles()[0]->getTitle() === 'Český název', 'Localized title resource mapping failed.');
expect($description->getFactSheets()[0]->getDescription() === 'Popis', 'Fact sheet resource mapping failed.');
expect($description->getPhotos()[0]->getUrl() === 'https://example.test/cover.jpg', 'Photo resource mapping failed.');
expect($description->getVideos()[0]->hasAgeWarning() === false, 'Video resource mapping failed.');
expect($description->getReleases()[0]->getReleaseDate() instanceof DateTimeImmutable, 'Release resource date mapping failed.');
expectException(static function (): void { (new ProductDescriptionItem((object) ['eans' => [123]]))->getEans(); }, ResourceMappingException::class);

$order = new OrderDetailItem((object) [
    'status' => 'completed',
    'createdOn' => '2026-08-01T08:00:00Z',
    'products' => [(object) ['codes' => [(object) ['codeType' => 'CODE_TEXT', 'code' => 'ABC']]]],
]);
expect($order->getStatusType() === OrderStatus::Completed, 'Order status enum mapping failed.');
expect($order->getCreatedAt() instanceof DateTimeImmutable, 'Order creation date mapping failed.');
expect(iterator_to_array($order->iterateProducts())[0]->getCodes()[0]->isText(), 'Nested order/code iteration failed.');

$pagedClient = new PagedClient([
    '' => (object) [
        'items' => [(object) ['productId' => 'p1'], (object) ['productId' => 'p2']],
        'continuationToken' => 'next',
    ],
    'next' => (object) [
        'items' => [(object) ['productId' => 'p3']],
        'continuationToken' => null,
    ],
]);
$ids = [];
foreach ((new ProductsApi($pagedClient))->iterate() as $product) {
    $ids[] = $product->getId();
}
expect($ids === ['p1', 'p2', 'p3'], 'Streaming iterator did not traverse all product pages.');

$continuation = new MemoryContinuationStorage();
$stream = (new ProductsApi($pagedClient, $continuation))->iterateWithContinuationStorage();
foreach ($stream as $product) {
    expect($product->getId() === 'p1', 'Unexpected first streamed product.');
    break;
}
unset($stream);
expect($continuation->history === [], 'A partially consumed page must not advance its continuation token.');

foreach ((new ProductsApi($pagedClient, $continuation))->iterateWithContinuationStorage() as $product) {
    // consume the complete stream
}
expect($continuation->history === ['next', null], 'Continuation token must be checkpointed after each complete page.');

fwrite(STDOUT, "All tests passed.\n");
