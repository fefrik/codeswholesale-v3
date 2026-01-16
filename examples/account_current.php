<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\AccountApi;

$client = require __DIR__ . '/bootstrap.php';

$acc = new AccountApi($client);
var_export($acc->getCurrent());

echo PHP_EOL;
