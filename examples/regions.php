<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\RegionsApi;

$client = require __DIR__ . '/bootstrap.php';

$regionsApi = new RegionsApi($client);
foreach ($regionsApi->getAll() as $r) {
    echo $r->getName() . PHP_EOL;
}
