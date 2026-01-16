<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\ProductsApi;
use CodesWholesaleApi\Resource\ProductItem;

$client = require __DIR__ . '/bootstrap.php';

$filters = [
    // vyber jen jedno:
    'updatedSince' => '2025-12-20T00:00:00Z',
    // 'createdSince' => '2025-12-20T00:00:00Z',
    // 'productIds' => ['ID1', 'ID2'],
];

$count = 0;

$productsApi = new ProductsApi($client);
$productsApi->getAll(
    function (array $items) use (&$count): bool {
        /** @var ProductItem[] $items */
        foreach ($items as $product) {
            // ProductItem je objekt vytvořený z API řádku
            echo $product->getId() . PHP_EOL; // nebo $product->id dle implementace
            $count++;
        }
        return true; // pokračuj na další stránku
    },
    $filters
);

echo "Total: {$count}" . PHP_EOL;
