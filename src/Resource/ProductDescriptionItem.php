<?php

namespace CodesWholesaleApi\Resource;

class ProductDescriptionItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getProductId(): ?string
    {
        return $this->data['productId'] ?? null;
    }

    public function getPlatform(): ?string
    {
        return $this->data['platform'] ?? null;
    }

    public function getOfficialTitle(): ?string
    {
        return $this->data['officialTitle'] ?? null;
    }

    public function getCategory(): ?string
    {
        return $this->data['category'] ?? null;
    }

    public function getDeveloperName(): ?string
    {
        return $this->data['developerName'] ?? null;
    }

    public function getDeveloperHomepage(): ?string
    {
        return $this->data['developerHomepage'] ?? null;
    }

    public function getEans(): array
    {
        return isset($this->data['eans']) && is_array($this->data['eans']) ? $this->data['eans'] : [];
    }

    public function getEditions(): array
    {
        return isset($this->data['editions']) && is_array($this->data['editions']) ? $this->data['editions'] : [];
    }

    public function getExtensionPacks(): array
    {
        return isset($this->data['extensionPacks']) && is_array($this->data['extensionPacks'])
            ? $this->data['extensionPacks']
            : [];
    }

    public function getKeywords(): ?string
    {
        return $this->data['keywords'] ?? null;
    }

    public function getMinimumRequirements(): ?string
    {
        return $this->data['minimumRequirements'] ?? null;
    }

    public function getRecommendedRequirements(): ?string
    {
        return $this->data['recommendedRequirements'] ?? null;
    }

    public function getInTheGameLanguages(): array
    {
        return isset($this->data['inTheGameLanguages']) && is_array($this->data['inTheGameLanguages'])
            ? $this->data['inTheGameLanguages']
            : [];
    }

    public function getLocalizedTitles(): array
    {
        return isset($this->data['localizedTitles']) && is_array($this->data['localizedTitles'])
            ? $this->data['localizedTitles']
            : [];
    }

    public function getFactSheets(): array
    {
        return isset($this->data['factSheets']) && is_array($this->data['factSheets'])
            ? $this->data['factSheets']
            : [];
    }

    public function getPhotos(): array
    {
        return isset($this->data['photos']) && is_array($this->data['photos'])
            ? $this->data['photos']
            : [];
    }

    public function getVideos(): array
    {
        return isset($this->data['videos']) && is_array($this->data['videos'])
            ? $this->data['videos']
            : [];
    }

    public function getReleases(): array
    {
        return isset($this->data['releases']) && is_array($this->data['releases'])
            ? $this->data['releases']
            : [];
    }

    public function getPegiRating(): ?string
    {
        return $this->data['pegirating'] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
