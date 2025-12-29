<?php

namespace CodesWholesaleApi\Resource;

class ProductImageItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Pokud API vrací třeba { "id": "..."} apod.
     */
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Často bývá URL nebo base64; nechávám obecně.
     */
    public function getImage(): ?string
    {
        return $this->data['image'] ?? null;
    }

    public function getFormat(): ?string
    {
        return $this->data['format'] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
