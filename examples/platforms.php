<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Platform;

$client = require __DIR__ . '/bootstrap.php';

foreach (Platform::getAll($client) as $p) {
    echo $p->getName() . PHP_EOL;
}
