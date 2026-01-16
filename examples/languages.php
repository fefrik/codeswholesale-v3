<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\LanguagesApi;

$client = require __DIR__ . '/bootstrap.php';

$languagesApi = new LanguagesApi($client);
$languages = $languagesApi->getAll();

foreach ($languages as $language) {
    echo $language->getName() . PHP_EOL;
}
