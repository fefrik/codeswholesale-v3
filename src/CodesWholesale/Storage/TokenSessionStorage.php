<?php

namespace CodesWholesale\Storage;

final class TokenSessionStorage implements StorageInterface
{
    private string $sessionKey;

    public function __construct(string $sessionKey = 'codeswholesale_token')
    {
        $this->sessionKey = $sessionKey;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function saveToken(array $tokenData): void
    {
        $_SESSION[$this->sessionKey] = $tokenData;
    }

    public function getToken(): ?array
    {
        $token = $_SESSION[$this->sessionKey] ?? null;

        if (!is_array($token)) {
            return null;
        }

        if (isset($token['expires_at']) && time() >= (int)$token['expires_at']) {
            unset($_SESSION[$this->sessionKey]);
            return null;
        }
        return $token;
    }

    public function clearToken(): void
    {
        unset($_SESSION[$this->sessionKey]);
    }
}
