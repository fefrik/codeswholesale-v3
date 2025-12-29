<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Order;

$client = require __DIR__ . '/bootstrap.php';

$request = [
    'allowPreOrder' => true,
    'orderId' => 'MY-ORDER-' . date('Ymd-His'),
    'products' => [
        [
            'productId' => 'PRODUCT_ID_1',
            'quantity' => 1,
            'price' => 9.99,
        ],
        // další položky...
    ],
];

$order = Order::create($client, $request);

if (!$order) {
    echo "Create failed\n";
    exit(1);
}

echo "Created orderId=" . $order->getOrderId() . PHP_EOL;
echo "Status=" . $order->getStatus() . PHP_EOL;
echo "Total=" . $order->getTotalPrice() . PHP_EOL;
