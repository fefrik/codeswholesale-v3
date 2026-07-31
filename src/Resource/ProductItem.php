<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Resource\Exceptions\NoImagesFoundException;

final class ProductItem extends Resource
{
    public function getId(): ?string
    {
        return $this->str('productId');
    }

    public function getIdentifier(): ?string
    {
        return $this->str('identifier');
    }

    public function getName(): ?string
    {
        return $this->str('name');
    }

    public function getStock(): ?int
    {
        return $this->int('quantity');
    }

    public function getPlatform(): ?string
    {
        return $this->str('platform');
    }

    /** @return array<int, ImageItem> */
    public function getImages(): array
    {
        return iterator_to_array($this->iterateImages(), false);
    }

    /** @return \Generator<int, ImageItem, void, void> */
    public function iterateImages(): \Generator
    {
        foreach ($this->iterateObjects('images') as $item) yield new ImageItem($item);
    }

    /**
     * @throws NoImagesFoundException
     */
    public function getImageUrl(string $format): string
    {
        foreach ($this->getImages() as $image) {
            $fmt = $image->getFormat();
            $url = $image->getUrl();

            if ($fmt === $format && $url !== null && $url !== '') {
                return $url;
            }
        }

        throw new NoImagesFoundException();
    }

    /** @return array<int, PriceItem> */
    public function getPrices(): array
    {
        return iterator_to_array($this->iteratePrices(), false);
    }

    /** @return \Generator<int, PriceItem, void, void> */
    public function iteratePrices(): \Generator
    {
        foreach ($this->iterateObjects('prices') as $item) yield new PriceItem($item);
    }

    public function getDefaultPrice(): ?float
    {
        foreach ($this->iteratePrices() as $price) {
            if ($price->getFrom() === 1) {
                return $price->getValue();
            }
        }
        return null;
    }

    /** @return array<int, string> */
    public function getRegions(): array
    {
        return $this->stringList('regions');
    }

    /** @return array<int, string> */
    public function getLanguages(): array
    {
        return $this->stringList('languages');
    }

    /** @return array<int, string> */
    public function getBadges(): array
    {
        return $this->stringList('badges');
    }

    public function getRegionDescription(): ?string
    {
        return $this->str('regionDescription');
    }

    public function getReleaseDateRaw(): ?string
    {
        return $this->str('releaseDate');
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->dateTime('releaseDate');
    }

    public function getReleaseDateFormatted(string $format = 'd/m/Y'): ?string
    {
        return $this->getReleaseDate()?->format($format);
    }
}
