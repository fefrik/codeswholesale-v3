<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\OrdersApi;

$client = require __DIR__ . '/bootstrap.php';

$startFrom = $argv[1] ?? null; // např. 2025-12-01T00:00:00Z
$endOn = $argv[2] ?? null;     // např. 2025-12-31T23:59:59Z

$ordersApi = new OrdersApi($client);
$orders = $ordersApi->getAll($startFrom, $endOn);
foreach ($orders as $o) {
    echo $o->getOrderId() . ' | ' . $o->getStatus() . ' | ' . $o->getTotalPrice() . PHP_EOL;
}
