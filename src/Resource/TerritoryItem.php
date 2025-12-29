<?php

namespace CodesWholesaleApi\Resource;

class TerritoryItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getTerritory(): ?string
    {
        return $this->data['territory'] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
