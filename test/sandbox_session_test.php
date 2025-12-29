<?php

declare(strict_types=1);

use Api\Client;
use Auth\TokenNormalizer;
use Config\Config;
use Storage\TokenSessionStorage;

require __DIR__ . '/../vendor/autoload.php';

// --- Credentials (SANDBOX) ---
$clientId = getenv('CWS_CLIENT_ID') ?: 'YOUR_CLIENT_ID';
$clientSecret = getenv('CWS_CLIENT_SECRET') ?: 'YOUR_CLIENT_SECRET';

// --- Storage (Session) ---
$storage = new TokenSessionStorage('codeswholesale_token');

// --- Config (SANDBOX) ---
$config = new Config(true);

// --- Client ---
$client = new Client(
    $config,
    $storage,
    $clientId,
    $clientSecret,
    new TokenNormalizer(60) // buffer 60s
);

echo "== 1) Forcing invalid token into SESSION to trigger 401 retry ==\n";

// uložíme neplatný token, ale s expirací v budoucnu, aby ho client použil
$storage->saveToken([
    'access_token' => 'THIS_IS_INVALID_TOKEN',
    'token_type'   => 'bearer',
    'expires_at'   => time() + 3600,
]);

echo "Bad token saved into session.\n";
echo "Session ID: " . session_id() . "\n\n";

echo "== 2) Calling /v3/accounts/current on SANDBOX ==\n";

try {
    $res = $client->get('/v3/accounts/current');

    echo "HTTP: " . $res['status'] . "\n";
    echo "Response data:\n";
    print_r($res['data']);

    echo "\n== OK: If you saw HTTP 200, the client refreshed token after 401 and retried. ==\n";
} catch (Throwable $e) {
    echo "\n== ERROR ==\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
