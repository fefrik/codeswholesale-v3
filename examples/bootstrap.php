<?php

declare(strict_types=1);

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Auth\TokenNormalizer;
use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Storage\OAuth2\TokenSessionOAuthStorage;

require __DIR__ . '/../vendor/autoload.php';

$config = new Config(); // nastavení API (sandbox = true)

$oauthStorage = new TokenSessionOAuthStorage();

$clientId = getenv('CW_CLIENT_ID') ?: 'YOUR_CLIENT_ID';
$clientSecret = getenv('CW_CLIENT_SECRET') ?: 'YOUR_CLIENT_SECRET';

$client = new Client(
    $config,
    $oauthStorage,
    $clientId,
    $clientSecret,
    new TokenNormalizer(),
    20,
    'CodesWholesaleClient/1.0'
);

return $client;
