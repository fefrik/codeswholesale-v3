<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\PlatformsApi;

$client = require __DIR__ . '/bootstrap.php';

$platformsApi = new PlatformsApi($client);
$platforms = $platformsApi->getAll();
foreach ($platforms as $p) {
    echo $p->getName() . PHP_EOL;
}
