<?php

namespace CodesWholesaleApi\Storage\LastSync;

final class FileLastSyncAtStorage implements LastSyncAtStorageInterface
{
    /** @var string */
    private $file;

    public function __construct(string $file = null)
    {
        $this->file = $file ?: (__DIR__ . '/products_last_sync_at.txt');
    }

    public function getLastSyncAt(): ?string
    {
        if (!file_exists($this->file)) {
            return null;
        }
        $t = trim((string) file_get_contents($this->file));
        return $t !== '' ? $t : null;
    }

    public function saveLastSyncAt(string $iso): void
    {
        file_put_contents($this->file, $iso, LOCK_EX);
    }

    public function clearLastSyncAt(): void
    {
        if (file_exists($this->file)) {
            @unlink($this->file);
        }
    }
}
