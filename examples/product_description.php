<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\ProductDescriptionApi;

$client = require __DIR__ . '/bootstrap.php';

$productId = $argv[1] ?? 'PRODUCT_ID_HERE';
$locale = $argv[2] ?? 'en';

$productDescriptionApi = new ProductDescriptionApi($client);
$productDescription = $productDescriptionApi->getByProductId($productId, $locale);

if ($productDescription === null) {
    echo "No description\n";
    exit(0);
}

echo $productDescription->getOfficialTitle() . PHP_EOL;
echo $productDescription->getDeveloperName() . PHP_EOL;
