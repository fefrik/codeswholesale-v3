<?php

namespace CodesWholesaleApi\Storage\OAuth2;

use PDO;

final class TokenDatabaseOAuthStorage implements OAuthStorageInterface
{
    private PDO $db;
    private string $table;
    private string $tokenKey;

    public function __construct(PDO $db, string $prefix = '', string $tokenKey = 'codeswholesale-config-id')
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->table = $prefix . 'access_tokens';
        $this->tokenKey = $tokenKey;
    }

    public function saveToken(array $tokenData): void
    {
        $sql = "INSERT INTO {$this->table} (client_config_id, access_token, token_type, expires_in)
                VALUES (:client_config_id, :access_token, :token_type, :expires_in)
                ON DUPLICATE KEY UPDATE
                    access_token = VALUES(access_token),
                    token_type   = VALUES(token_type),
                    expires_in   = VALUES(expires_in)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':client_config_id', $this->tokenKey, PDO::PARAM_STR);
        $stmt->bindValue(':access_token', (string)$tokenData['access_token'], PDO::PARAM_STR);
        $stmt->bindValue(':token_type', (string)($tokenData['token_type'] ?? 'bearer'), PDO::PARAM_STR);
        $stmt->bindValue(':expires_in', (int)$tokenData['expires_at'], PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getToken(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT access_token, token_type, expires_in
             FROM {$this->table}
             WHERE client_config_id = :client_config_id
             LIMIT 1"
        );
        $stmt->bindValue(':client_config_id', $this->tokenKey, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        if (time() >= (int)$row['expires_in']) {
            $this->clearToken();
            return null;
        }

        return [
            'access_token' => $row['access_token'],
            'token_type'   => $row['token_type'],
            'expires_in'   => (int)$row['expires_in'],
        ];
    }

    public function clearToken(): void
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE client_config_id = :client_config_id");
        $stmt->bindValue(':client_config_id', $this->tokenKey, PDO::PARAM_STR);
        $stmt->execute();
    }
}
