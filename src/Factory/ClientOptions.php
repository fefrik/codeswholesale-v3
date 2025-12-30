<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Auth\TokenNormalizer;

final class ClientOptions
{
    /** @var TokenNormalizer|null */
    public $normalizer;

    /** @var int */
    public $timeoutSeconds;

    /** @var string */
    public $userAgent;

    public function __construct(
        TokenNormalizer $normalizer = null,
        int $timeoutSeconds = 20,
        string $userAgent = 'CodesWholesaleClient/1.0'
    ) {
        $this->normalizer = $normalizer;
        $this->timeoutSeconds = $timeoutSeconds;
        $this->userAgent = $userAgent;
    }
}
