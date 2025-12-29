<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Product;
use CodesWholesaleApi\Resource\ProductItem;

$client = require __DIR__ . '/bootstrap.php';

$filters = [
    // vyber jen jedno:
    'updatedSince' => '2025-12-20T00:00:00Z',
    // 'createdSince' => '2025-12-20T00:00:00Z',
    // 'productIds' => ['ID1', 'ID2'],
];

$count = 0;

Product::getAll($client, function (array $items, ?string $nextToken) use (&$count) {
    foreach ($items as $row) {
        $p = new ProductItem($row);
        $count++;
        echo $p->getId() . ' | ' . $p->getName() . PHP_EOL;
    }

    echo "Page done. nextToken=" . ($nextToken ?: 'NULL') . PHP_EOL;
}, $filters);

echo "Total: {$count}" . PHP_EOL;
