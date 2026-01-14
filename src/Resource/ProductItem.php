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

    public function getIdentifier(): ?string
    {
        return $this->data['identifier'] ?? null;
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

    public function getImages(): array
    {
        return isset($this->data['images']) && is_array($this->data['images'])
            ? $this->data['images']
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

    public function getLanguages(): array
    {
        return isset($this->data['languages']) && is_array($this->data['languages'])
            ? $this->data['languages']
            : [];
    }

    public function getBadges(): array
    {
        return isset($this->data['badges']) && is_array($this->data['badges'])
            ? $this->data['badges']
            : [];
    }

    public function getRegionDescription(): ?string
    {
        return $this->data['regionDescription'] ?? null;
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
