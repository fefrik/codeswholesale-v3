<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Product;
use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Storage\ContinuationToken\FileContinuationTokenStorage;

$client = require __DIR__ . '/bootstrap.php';

$filters = [
    'updatedSince' => '2025-12-20T00:00:00Z',
];

// doporučeno: token “svázat” s filtry (jiný soubor pro jiné filtry)
$key = md5(json_encode($filters));
$tokenStorage = new FileContinuationTokenStorage(__DIR__ . "/products_token_{$key}.txt");

// pokud máš v Product statickou konfiguraci:
Product::configureContinuationTokenStorage($tokenStorage);

$count = 0;

Product::getAllWithContinuationStorage(
    $client,
    function (array $items, ?string $nextToken) use (&$count) {
        foreach ($items as $row) {
            $p = new ProductItem($row);
            // TODO: upsert do DB
            $count++;
        }

        echo "Processed page, items=" . count($items) . ", nextToken=" . ($nextToken ?: 'NULL') . PHP_EOL;
    },
    $filters
);

echo "Total processed: {$count}" . PHP_EOL;
