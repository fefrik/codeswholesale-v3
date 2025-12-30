<?php

namespace CodesWholesaleApi\Factory;

use CodesWholesaleApi\Auth\TokenNormalizer;

final class ClientOptions
{
    /** @var int */
    public $expirationBuffer;

    /** @var int */
    public $timeoutSeconds;

    /** @var string */
    public $userAgent;

    /** @var TokenNormalizer */
    public $normalizer;

    public function __construct(
        int $expirationBuffer = 60,
        int $timeoutSeconds = 20,
        string $userAgent = 'CodesWholesaleClient/1.0'
    ) {
        $this->expirationBuffer = $expirationBuffer;
        $this->timeoutSeconds   = $timeoutSeconds;
        $this->userAgent        = $userAgent;
        $this->normalizer       = new TokenNormalizer($expirationBuffer);
    }

    /**
     * Vlastní normalizer (výjimečně),
     * např. pokud chcete změnit expirationBuffer dynamicky.
     */
    public function withNormalizer(TokenNormalizer $normalizer): self
    {
        $this->normalizer = $normalizer;
        return $this;
    }
}
