<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\ProductsApi;
use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Storage\ContinuationToken\FileContinuationTokenStorage;

$client = require __DIR__ . '/bootstrap.php';

$filters = [
    'status' => 'ACTIVE',
];

$productsApi = new ProductsApi($client);
$productsApi->getAll(
    function (array $items, ?string $continuationToken) {
        /** @var ProductItem[] $items */
        foreach ($items as $product) {
            // ProductItem je objekt vytvořený z API řádku
            echo $product->getId() . PHP_EOL; // nebo $product->id dle implementace
        }
        echo "Next continuation token: " . ($continuationToken ?? 'null') . PHP_EOL;
        return true; // pokračuj na další stránku
    },
    $filters
);
