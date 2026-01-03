<?php

namespace CodesWholesaleApi;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Factory\ClientFactory;
use CodesWholesaleApi\Factory\ClientOptions;
use InvalidArgumentException;
use PDO;

final class CodesWholesale
{
    public const LIVE_ENDPOINT    = 'https://api.codeswholesale.com';
    public const SANDBOX_ENDPOINT = 'https://sandbox.codeswholesale.com';

    private function __construct() {}

    /**
     * Nejjednodušší varianta: když už máš Client, jen ho “obalíš” do SDK.
     */
    public static function sdk(Client $client)
    {
        return new Sdk($client);
    }

    /**
     * DX varianta: vytvoří Client přes existující ClientFactory a vrátí SDK.
     *
     * @param string $mode Mode::LIVE|Mode::SANDBOX
     * @param string $storageMode OAuthStorageMode::DB|OAuthStorageMode::SESSION
     */
    public static function create(
        string        $mode,
        string        $storageMode,
                      $clientId,
                      $clientSecret,
        PDO           $pdo = null,
                      $tokenKey = null,
                      $sessionKey = null,
        ClientOptions $options = null
    ): Sdk
    {
        $client = self::createClient(
            $mode,
            $storageMode,
            $clientId,
            $clientSecret,
            $pdo,
            $tokenKey,
            $sessionKey,
            $options
        );

        return new Sdk($client);
    }

    private static function createClient(
        $mode,
        $storageMode,
        $clientId,
        $clientSecret,
        PDO $pdo = null,
        $tokenKey = null,
        $sessionKey = null,
        ClientOptions $options = null
    ): Client
    {
        if ($storageMode === OAuthStorageMode::DB && $pdo === null) {
            throw new InvalidArgumentException('PDO is required when using DB OAuth storage.');
        }

        if ($mode !== Mode::LIVE && $mode !== Mode::SANDBOX) {
            throw new InvalidArgumentException('Invalid mode. Use Mode::LIVE or Mode::SANDBOX.');
        }

        if ($storageMode !== OAuthStorageMode::DB && $storageMode !== OAuthStorageMode::SESSION) {
            throw new InvalidArgumentException('Invalid storage mode. Use OAuthStorageMode::DB or OAuthStorageMode::SESSION.');
        }

        // defaults
        if ($tokenKey === null) {
            $tokenKey = 'codeswholesale-config-id';
        }
        if ($sessionKey === null) {
            $sessionKey = 'codeswholesale.oauth_token';
        }

        // Vyrob klienta pomocí existujícího ClientFactory
        if ($mode === Mode::LIVE && $storageMode === OAuthStorageMode::DB) {
            return ClientFactory::liveDb($pdo, $clientId, $clientSecret, $tokenKey, $options);
        }

        if ($mode === Mode::SANDBOX && $storageMode === OAuthStorageMode::DB) {
            return ClientFactory::sandboxDb($pdo, $clientId, $clientSecret, $tokenKey, $options);
        }

        if ($mode === Mode::LIVE && $storageMode === OAuthStorageMode::SESSION) {
            return ClientFactory::liveSession($clientId, $clientSecret, $sessionKey, $options);
        }

        // sandbox + session
        return ClientFactory::sandboxSession($clientId, $clientSecret, $sessionKey, $options);
    }
}

final class Mode
{
    public const LIVE = 'live';
    public const SANDBOX = 'sandbox';

    private function __construct() {}
}

final class OAuthStorageMode
{
    public const DB = 'db';
    public const SESSION = 'session';

    private function __construct() {}
}