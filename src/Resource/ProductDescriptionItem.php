<?php

namespace CodesWholesaleApi\Resource;

final class ProductDescriptionItem extends Resource
{
    public function getProductId(): ?string
    {
        return $this->str('productId');
    }

    public function getPlatform(): ?string
    {
        return $this->str('platform');
    }

    public function getOfficialTitle(): ?string
    {
        return $this->str('officialTitle');
    }

    public function getCategory(): ?string
    {
        return $this->str('category');
    }

    public function getDeveloperName(): ?string
    {
        return $this->str('developerName');
    }

    public function getDeveloperHomepage(): ?string
    {
        return $this->str('developerHomepage');
    }

    /**
     * @return array
     */
    public function getEans(): array
    {
        return $this->scalarArray('eans');
    }

    /**
     * @return array
     */
    public function getEditions(): array
    {
        return $this->scalarArray('editions');
    }

    /**
     * @return array
     */
    public function getExtensionPacks(): array
    {
        return $this->scalarArray('extensionPacks');
    }

    public function getKeywords(): ?string
    {
        return $this->str('keywords');
    }

    public function getMinimumRequirements(): ?string
    {
        return $this->str('minimumRequirements');
    }

    public function getRecommendedRequirements(): ?string
    {
        return $this->str('recommendedRequirements');
    }

    /**
     * @return array
     */
    public function getInTheGameLanguages(): array
    {
        return $this->scalarArray('inTheGameLanguages');
    }

    /**
     * @return array
     */
    public function getLocalizedTitles(): array
    {
        return $this->scalarArray('localizedTitles');
    }

    /**
     * @return array
     */
    public function getFactSheets(): array
    {
        return $this->scalarArray('factSheets');
    }

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return $this->scalarArray('photos');
    }

    /**
     * @return array
     */
    public function getVideos(): array
    {
        return $this->scalarArray('videos');
    }

    /**
     * @return array
     */
    public function getReleases(): array
    {
        return $this->scalarArray('releases');
    }

    public function getPegiRating(): ?string
    {
        return $this->str('pegirating');
    }
}
