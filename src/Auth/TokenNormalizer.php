<?php

namespace CodesWholesaleApi\Auth;

final class TokenNormalizer
{
    /** @var int */
    private $expirationBuffer;

    public function __construct(int $expirationBuffer = 60)
    {
        $this->expirationBuffer = $expirationBuffer;
    }

    public function normalize(array $token): array
    {
        // CodesWholesale wrapper response
        if (isset($token['value'])) {
            return $this->normalizeWrapper($token);
        }

        // fallback (kdyby někde vraceli klasiku)
        if (isset($token['access_token'])) {
            return $this->normalizeOAuth($token);
        }

        throw new \InvalidArgumentException('Unknown token format.');
    }

    private function normalizeWrapper(array $token): array
    {
        if (empty($token['value'])) {
            throw new \InvalidArgumentException('Missing token "value".');
        }

        // expired=true -> rovnou propadne jako expirovaný
        $expiresAt = time();

        if (empty($token['expired'])) {
            // preferuj absolutní expiration
            if (!empty($token['expiration']) && is_string($token['expiration'])) {
                try {
                    $dt = new \DateTimeImmutable($token['expiration']);
                    $expiresAt = $dt->getTimestamp();
                } catch (\Exception $e) {
                    // fallback na expiresIn
                    $expiresIn = isset($token['expiresIn']) ? (int)$token['expiresIn'] : 0;
                    $expiresAt = time() + max(0, $expiresIn);
                }
            } else {
                $expiresIn = isset($token['expiresIn']) ? (int)$token['expiresIn'] : 0;
                $expiresAt = time() + max(0, $expiresIn);
            }
        }

        return array(
            'access_token' => (string)$token['value'],
            'token_type'   => (string)($token['tokenType'] ?? 'bearer'),
            'expires_at'   => max(time(), (int)$expiresAt - $this->expirationBuffer),
            'scope'        => $token['scope'] ?? null,
        );
    }

    private function normalizeOAuth(array $token): array
    {
        $expiresIn = isset($token['expires_in']) ? (int)$token['expires_in'] : 0;

        return array(
            'access_token' => (string)$token['access_token'],
            'token_type'   => (string)($token['token_type'] ?? 'bearer'),
            'expires_at'   => time() + max(0, $expiresIn - $this->expirationBuffer),
            'scope'        => $token['scope'] ?? null,
        );
    }
}
