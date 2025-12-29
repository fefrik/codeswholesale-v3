<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\ProductDescription;

$client = require __DIR__ . '/bootstrap.php';

$productId = $argv[1] ?? 'PRODUCT_ID_HERE';
$locale = $argv[2] ?? 'en';

$desc = ProductDescription::getByProductId($client, $productId, $locale);

if (!$desc) {
    echo "No description\n";
    exit(0);
}

echo $desc->getOfficialTitle() . PHP_EOL;
echo $desc->getDeveloperName() . PHP_EOL;
