<?php

namespace CodesWholesaleApi\Resource;

class ProductItem
{

    private array $data;


    /**
     * Constructor for ProductItem
     *
     * @param array $data Product data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }


    /**
     * Get product ID
     *
     * @return string|null Product ID
     */
    public function getId(): ?string
    {
        return $this->data['productId'] ?? null;
    }


    /**
     * Get product name
     *
     * @return string|null Product name
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }


    /**
     * Get product prices
     *
     * @return array|null List of prices
     */
    public function getPrices(): ?array
    {
        return $this->data['prices'] ?? null;
    }


    /**
     * Get default price for quantity 1
     *
     * @return float|null Default price
     */
    public function getDefaultPrice(): ?float
    {

        $prices = $this->getPrices();

        if (!$prices) return null;

        foreach ($prices as $price) {

            if ($price['from'] == 1) {
                return $price['value'];
            }

        }

        return null;

    }

    /**
     * Get available stock quantity
     *
     * @return int|null Stock quantity
     */
    public function getStock(): ?int
    {
        return $this->data['quantity'] ?? null;
    }


    /**
     * Get the platform name
     *
     * @return string|null Platform
     */
    public function getPlatform(): ?string
    {
        return $this->data['platform'] ?? null;
    }


    /**
     * Get available regions
     *
     * @return array|null List of regions
     */
    public function getRegions(): ?array
    {
        return $this->data['regions'] ?? null;
    }

    /**
     * Get the release date
     *
     * @return string|null Release date
     */
    public function getReleaseDate(): ?string
    {

        if (empty($this->data['releaseDate'])) {
            return null;
        }

        $timestamp = strtotime($this->data['releaseDate']);
        if ($timestamp === false) {
            return null;
        }

        return date('d/m/Y', $timestamp);

    }

}