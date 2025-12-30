<?php

namespace CodesWholesaleApi\Config;

use CodesWholesaleApi\CodesWholesale;

final class Config
{
    /** @var bool */
    private bool $sandbox;

    public function __construct(bool $sandbox = true)
    {
        $this->sandbox = $sandbox;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function getApiBaseUrl(): string
    {
        return $this->sandbox
            ? CodesWholesale::SANDBOX_ENDPOINT
            : CodesWholesale::LIVE_ENDPOINT;
    }

    public function getOauthTokenUrl(): string
    {
        return $this->getApiBaseUrl() . '/oauth/token';
    }

    public static function live(): self
    {
        return new self(false);
    }

    public static function sandbox(): self
    {
        return new self(true);
    }
}
