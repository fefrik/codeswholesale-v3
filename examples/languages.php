<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Language;

$client = require __DIR__ . '/bootstrap.php';

foreach (Language::getAll($client) as $l) {
    echo $l->getName() . PHP_EOL;
}
