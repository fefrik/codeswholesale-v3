<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Territory;

$client = require __DIR__ . '/bootstrap.php';

foreach (Territory::getAll($client) as $t) {
    echo $t->getTerritory() . PHP_EOL;
}
