<?php
declare(strict_types=1);

use CodesWholesaleApi\Resource\Security;

$client = require __DIR__ . '/bootstrap.php';

$result = Security::check($client, [
    'customerEmail' => 'customer@example.com',
    'customerIpAddress' => '203.0.113.10',
    'customerPaymentEmail' => 'pay@example.com',
    'customerUserAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
]);

if (!$result) {
    echo "No result\n";
    exit(0);
}

echo "RiskScore: " . $result->getRiskScore() . PHP_EOL;
echo "IP blacklisted: " . (int)$result->isIpBlacklisted() . PHP_EOL;
echo "Domain blacklisted: " . (int)$result->isDomainBlacklisted() . PHP_EOL;
