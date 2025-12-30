<?php

namespace CodesWholesaleApi\Factory;

use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Storage\OAuth2\OAuthStorageInterface;
use CodesWholesaleApi\Storage\OAuth2\TokenDatabaseOAuthStorage;
use CodesWholesaleApi\Storage\OAuth2\TokenSessionOAuthStorage;
use PDO;

final class ClientFactory {

    // --- Nejkratší cesta (default DB) ---
    public static function live(
        PDO $pdo,
        string $clientId,
        string $clientSecret,
        string $tokenKey = 'codeswholesale-config-id',
        ClientOptions $options = null
    ): Client {
        return self::liveDb($pdo, $clientId, $clientSecret, $tokenKey, $options);
    }

    public static function sandbox(
        PDO $pdo,
        string $clientId,
        string $clientSecret,
        string $tokenKey = 'codeswholesale-config-id',
        ClientOptions $options = null
    ): Client {
        return self::sandboxDb($pdo, $clientId, $clientSecret, $tokenKey, $options);
    }

    // --- DB explicit ---
    public static function liveDb(
        PDO $pdo,
        string $clientId,
        string $clientSecret,
        string $tokenKey = 'codeswholesale-config-id',
        ClientOptions $options = null
    ): Client {
        $storage = new TokenDatabaseOAuthStorage($pdo, $tokenKey);
        return self::make(Config::live(), $storage, $clientId, $clientSecret, $options);
    }

    public static function sandboxDb(
        PDO $pdo,
        string $clientId,
        string $clientSecret,
        string $tokenKey = 'codeswholesale-config-id',
        ClientOptions $options = null
    ): Client {
        $storage = new TokenDatabaseOAuthStorage($pdo, $tokenKey);
        return self::make(Config::sandbox(), $storage, $clientId, $clientSecret, $options);
    }

    // --- Session ---
    public static function liveSession(
        string $clientId,
        string $clientSecret,
        string $sessionKey = 'codeswholesale.oauth_token',
        ClientOptions $options = null
    ): Client {
        $storage = new TokenSessionOAuthStorage($sessionKey);
        return self::make(Config::live(), $storage, $clientId, $clientSecret, $options);
    }

    public static function sandboxSession(
        string $clientId,
        string $clientSecret,
        string $sessionKey = 'codeswholesale.oauth_token',
        ClientOptions $options = null
    ): Client {
        $storage = new TokenSessionOAuthStorage($sessionKey);
        return self::make(Config::sandbox(), $storage, $clientId, $clientSecret, $options);
    }

    // --- Shared builder ---
    private static function make(
        Config $config,
        OAuthStorageInterface $storage,
        string $clientId,
        string $clientSecret,
        ClientOptions $options = null
    ): Client {
        $o = $options ?: new ClientOptions();

        return new Client(
            $config,
            $storage,
            $clientId,
            $clientSecret,
            $o->normalizer,
            $o->timeoutSeconds,
            $o->userAgent
        );
    }
}
