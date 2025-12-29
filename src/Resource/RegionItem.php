<?php

namespace CodesWholesaleApi\Resource;

class RegionItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
