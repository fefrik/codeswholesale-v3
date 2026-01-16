<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\TerritoriesApi;

$client = require __DIR__ . '/bootstrap.php';

$territoriesApi = new TerritoriesApi($client);

foreach ($territoriesApi->getAll() as $t) {
    echo $t->getTerritory() . PHP_EOL;
}
