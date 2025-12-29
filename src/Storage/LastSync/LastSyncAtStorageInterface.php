<?php

namespace CodesWholesaleApi\Storage\LastSync;

interface LastSyncAtStorageInterface
{
    public function getLastSyncAt(): ?string;     // ISO-8601
    public function saveLastSyncAt(string $iso): void;
    public function clearLastSyncAt(): void;
}
