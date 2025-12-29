<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Region;

$client = require __DIR__ . '/bootstrap.php';

foreach (Region::getAll($client) as $r) {
    echo $r->getName() . PHP_EOL;
}
