<?php
declare(strict_types=1);

use CodesWholesaleApi\Storage\ContinuationToken\FileContinuationTokenStorage;
use CodesWholesaleApi\Storage\LastSync\FileLastSyncAtStorage;
use CodesWholesaleApi\Sync\ProductSyncRunner;

$client = require __DIR__ . '/bootstrap.php';

// 1) continuationToken (cursor v rámci běhu)
$continuation = new FileContinuationTokenStorage(__DIR__ . '/sync_products_continuation.txt');

// 2) lastSyncAt (logický checkpoint)
$lastSync = new FileLastSyncAtStorage(__DIR__ . '/sync_products_last_sync_at.txt');

$runner = new ProductSyncRunner($client, $continuation, $lastSync, 5);

// Jednorázový běh (ideální pro cron)
$runner->runOnce(function ($product) {
    echo $product->getId() . ' - ' . $product->getName() . PHP_EOL;
}, '2025-12-20T00:00:00Z'); // fallback pro úplně první běh
