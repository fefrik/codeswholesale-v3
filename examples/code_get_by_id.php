<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\CodesApi;

$client = require __DIR__ . '/bootstrap.php';

$codeId = $argv[1] ?? 'CODE_ID_HERE';

$codesApi = new CodesApi($client);
$code = $codesApi->getById($codeId);

if ($code === null) {
    echo "Code not found." . PHP_EOL;
    exit(1);
}

echo $code->getCodeType() . PHP_EOL;
echo $code->getCode() . PHP_EOL;
