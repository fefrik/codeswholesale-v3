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

    /** @return list<string> */
    public function getEans(): array
    {
        return $this->stringList('eans');
    }

    /**
     * @return list<string>
     */
    public function getEditions(): array
    {
        return $this->stringList('editions');
    }

    /**
     * @return list<string>
     */
    public function getExtensionPacks(): array
    {
        return $this->stringList('extensionPacks');
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
     * @return list<string>
     */
    public function getInTheGameLanguages(): array
    {
        return $this->stringList('inTheGameLanguages');
    }

    /** @return list<LocalizedTitleItem> */
    public function getLocalizedTitles(): array
    {
        return iterator_to_array($this->iterateLocalizedTitles(), false);
    }

    /** @return \Generator<int, LocalizedTitleItem, void, void> */
    public function iterateLocalizedTitles(): \Generator
    {
        foreach ($this->iterateObjects('localizedTitles') as $item) yield new LocalizedTitleItem($item);
    }

    /** @return list<FactSheetItem> */
    public function getFactSheets(): array
    {
        return iterator_to_array($this->iterateFactSheets(), false);
    }

    /** @return \Generator<int, FactSheetItem, void, void> */
    public function iterateFactSheets(): \Generator
    {
        foreach ($this->iterateObjects('factSheets') as $item) yield new FactSheetItem($item);
    }

    /** @return list<PhotoItem> */
    public function getPhotos(): array
    {
        return iterator_to_array($this->iteratePhotos(), false);
    }

    /** @return \Generator<int, PhotoItem, void, void> */
    public function iteratePhotos(): \Generator
    {
        foreach ($this->iterateObjects('photos') as $item) yield new PhotoItem($item);
    }

    /** @return list<VideoItem> */
    public function getVideos(): array
    {
        return iterator_to_array($this->iterateVideos(), false);
    }

    /** @return \Generator<int, VideoItem, void, void> */
    public function iterateVideos(): \Generator
    {
        foreach ($this->iterateObjects('videos') as $item) yield new VideoItem($item);
    }

    /** @return list<ReleaseItem> */
    public function getReleases(): array
    {
        return iterator_to_array($this->iterateReleases(), false);
    }

    /** @return \Generator<int, ReleaseItem, void, void> */
    public function iterateReleases(): \Generator
    {
        foreach ($this->iterateObjects('releases') as $item) yield new ReleaseItem($item);
    }

    public function getPegiRating(): ?string
    {
        return $this->str('pegirating');
    }
}
