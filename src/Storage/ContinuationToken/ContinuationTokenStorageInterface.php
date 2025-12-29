<?php

namespace CodesWholesaleApi\Storage\ContinuationToken;

interface ContinuationTokenStorageInterface
{
    public function getToken(): ?string;

    public function saveToken(?string $token): void;

    public function clearToken(): void;
}
