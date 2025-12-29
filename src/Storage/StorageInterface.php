<?php

namespace CodesWholesaleApi\Storage;

interface StorageInterface
{
    public function saveToken(array $tokenData): void;
    public function getToken(): ?array;      // vrací jen platný token, jinak null
    public function clearToken(): void;
}
