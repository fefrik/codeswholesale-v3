<?php
declare(strict_types=1);

use CodesWholesaleApi\Api\SecurityApi;

$client = require __DIR__ . '/bootstrap.php';

$securityCheckApi = new SecurityApi($client);
$result = $securityCheckApi->check([
    'customerEmail' => 'customer@example.com',
    'customerIpAddress' => '203.0.113.10',
    'customerPaymentEmail' => 'pay@example.com',
    'customerUserAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'cli',
]);

if ($result === null) {
    echo "No result\n";
    exit(0);
}

echo "RiskScore: " . $result->getRiskScore() . PHP_EOL;
echo "IP blacklisted: " . (int)$result->isIpBlacklisted() . PHP_EOL;
echo "Domain blacklisted: " . (int)$result->isDomainBlacklisted() . PHP_EOL;
