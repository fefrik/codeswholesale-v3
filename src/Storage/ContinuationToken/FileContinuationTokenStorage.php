<?php

namespace CodesWholesaleApi\Storage\ContinuationToken;

final class FileContinuationTokenStorage implements ContinuationTokenStorageInterface
{
    /** @var string */
    private $file;

    public function __construct(string $file = null)
    {
        $this->file = $file ?: (__DIR__ . '/products_continuation_token.txt');
    }

    public function getToken(): ?string
    {
        if (!file_exists($this->file)) {
            return null;
        }

        $t = trim((string) file_get_contents($this->file));
        return $t !== '' ? $t : null;
    }

    public function saveToken(?string $token): void
    {
        if ($token !== null && $token !== '') {
            file_put_contents($this->file, $token, LOCK_EX);
            return;
        }

        $this->clearToken();
    }

    public function clearToken(): void
    {
        if (file_exists($this->file)) {
            @unlink($this->file);
        }
    }
}
