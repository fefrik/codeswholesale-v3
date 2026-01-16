<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\ProductImagesApi;

$client = require __DIR__ . '/bootstrap.php';

$imageId = $argv[1] ?? 'IMAGE_ID_HERE';

$productImageApi = new ProductImagesApi($client);
$productImage = $productImageApi->getById($imageId);
var_export($productImage ? $productImage->getImage() : null);
echo PHP_EOL;
