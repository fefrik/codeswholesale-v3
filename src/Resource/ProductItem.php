<?php

namespace CodesWholesaleApi\Resource;

class ProductItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getId(): ?string
    {
        return $this->data['productId'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getPrices(): array
    {
        return isset($this->data['prices']) && is_array($this->data['prices'])
            ? $this->data['prices']
            : [];
    }

    public function getDefaultPrice(): ?float
    {
        foreach ($this->getPrices() as $price) {
            if (
                isset($price['from'], $price['value']) &&
                (int) $price['from'] === 1
            ) {
                return (float) $price['value'];
            }
        }

        return null;
    }

    public function getStock(): ?int
    {
        return isset($this->data['quantity'])
            ? (int) $this->data['quantity']
            : null;
    }

    public function getPlatform(): ?string
    {
        return $this->data['platform'] ?? null;
    }

    public function getRegions(): array
    {
        return isset($this->data['regions']) && is_array($this->data['regions'])
            ? $this->data['regions']
            : [];
    }

    public function getReleaseDateRaw(): ?string
    {
        return $this->data['releaseDate'] ?? null;
    }

    public function getReleaseDateFormatted(string $format = 'd/m/Y'): ?string
    {
        if (empty($this->data['releaseDate'])) {
            return null;
        }

        $timestamp = strtotime($this->data['releaseDate']);
        if ($timestamp === false) {
            return null;
        }

        return date($format, $timestamp);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
