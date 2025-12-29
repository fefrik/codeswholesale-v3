<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Product;

$client = require __DIR__ . '/bootstrap.php';

$productId = $argv[1] ?? 'PRODUCT_ID_HERE';

$p = Product::getById($client, $productId);

if (!$p) {
    echo "Not found\n";
    exit(0);
}

var_export($p->toArray());
echo PHP_EOL;
