<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Code;

$client = require __DIR__ . '/bootstrap.php';

$codeId = $argv[1] ?? 'CODE_ID_HERE';

$code = Code::getById($client, $codeId);

if (!$code) {
    echo "Not found\n";
    exit(0);
}

echo $code->getCodeType() . PHP_EOL;
echo $code->getCode() . PHP_EOL;
