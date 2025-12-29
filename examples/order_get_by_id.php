<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Order;

$client = require __DIR__ . '/bootstrap.php';

$orderId = $argv[1] ?? 'ORDER_ID_HERE';

$order = Order::getById($client, $orderId);

if (!$order) {
    echo "Not found\n";
    exit(0);
}

echo "Order: " . $order->getOrderId() . PHP_EOL;
echo "Status: " . $order->getStatus() . PHP_EOL;

foreach ($order->getProducts() as $p) {
    echo "- " . $p->getProductId() . " | " . $p->getName() . " | " . $p->getUnitPrice() . PHP_EOL;

    foreach ($p->getCodes() as $c) { // CodeItem (sjednocené)
        echo "  * " . $c->getCodeType() . " | " . $c->getCodeId() . PHP_EOL;
    }
}
