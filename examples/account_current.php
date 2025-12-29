<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Account;

$client = require __DIR__ . '/bootstrap.php';

$acc = Account::getCurrent($client);

if (!$acc) {
    echo "No account\n";
    exit(0);
}

var_export($acc->toArray());
echo PHP_EOL;
