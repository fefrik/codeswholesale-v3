<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\ProductsApi;

$client = require __DIR__ . '/bootstrap.php';

$productId = $argv[1] ?? 'PRODUCT_ID_HERE';

$productsApi = new ProductsApi($client);
$product = $productsApi->getById($productId);

if ($product === null) {
    echo "Not found\n";
    exit(0);
}

var_export($product->toArray());

echo PHP_EOL;
