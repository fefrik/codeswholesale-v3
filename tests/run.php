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
use CodesWholesaleApi\Mode;
use CodesWholesaleApi\OAuthStorageMode;
use CodesWholesaleApi\Resource\CodeItem;
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
$path = $code->saveImageBase64($tempDir);
expect(basename($path) === 'key.png', 'Image filename traversal was not removed.');
expect(file_get_contents($path) === 'image-data', 'Image code content was not saved correctly.');
expectException(static function () use ($code, $tempDir): void { $code->saveImageBase64($tempDir); }, RuntimeException::class);
unlink($path);
rmdir($tempDir);

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
