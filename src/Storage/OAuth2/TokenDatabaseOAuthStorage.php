<?php

namespace CodesWholesaleApi\Storage\OAuth2;

use PDO;

final class TokenDatabaseOAuthStorage implements OAuthStorageInterface
{
    private PDO $db;
    private string $table;
    private string $tokenKey;

    public function __construct(PDO $db, string $prefix = '', string $tokenKey = 'default')
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->table = $prefix . 'access_tokens';
        $this->tokenKey = $tokenKey;
    }

    public function saveToken(array $tokenData): void
    {
        $sql = "INSERT INTO {$this->table} (token_key, access_token, token_type, expires_at)
                VALUES (:token_key, :access_token, :token_type, :expires_at)
                ON DUPLICATE KEY UPDATE
                    access_token = VALUES(access_token),
                    token_type   = VALUES(token_type),
                    expires_at   = VALUES(expires_at)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token_key', $this->tokenKey, PDO::PARAM_STR);
        $stmt->bindValue(':access_token', (string)$tokenData['access_token'], PDO::PARAM_STR);
        $stmt->bindValue(':token_type', (string)($tokenData['token_type'] ?? 'bearer'), PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', (int)$tokenData['expires_at'], PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getToken(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT access_token, token_type, expires_at
             FROM {$this->table}
             WHERE token_key = :token_key
             LIMIT 1"
        );
        $stmt->bindValue(':token_key', $this->tokenKey, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        if (time() >= (int)$row['expires_at']) {
            $this->clearToken();
            return null;
        }

        return [
            'access_token' => $row['access_token'],
            'token_type'   => $row['token_type'],
            'expires_at'   => (int)$row['expires_at'],
        ];
    }

    public function clearToken(): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE token_key = :token_key");
        $stmt->bindValue(':token_key', $this->tokenKey, PDO::PARAM_STR);
        $stmt->execute();
    }
}
