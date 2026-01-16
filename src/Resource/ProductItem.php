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
        return array_map(
            function (\stdClass $i) {
                return new ImageItem($i);
            },
            $this->list('images')
        );
    }

    /**
     * @throws NoImagesFoundException
     */
    public function getImageUrl(string $format): string
    {
        foreach ($this->getImages() as $image) {
            $fmt = $image->getFormat();
            $url = $image->getUrl();

            if ($fmt === $format && $url !== '') {
                return $url;
            }
        }

        throw new NoImagesFoundException();
    }

    /** @return array<int, PriceItem> */
    public function getPrices(): array
    {
        return array_map(
            function (\stdClass $p) {
                return new PriceItem($p);
            },
            $this->list('prices')
        );
    }

    public function getDefaultPrice(): ?float
    {
        foreach ($this->getPrices() as $price) {
            if ($price->getFrom() === 1) {
                return $price->getValue();
            }
        }
        return null;
    }

    /** @return array<int, string> */
    public function getRegions(): array
    {
        // pokud nechceš filtrování na stringy, vrať jen scalarArray('regions')
        return array_values(array_filter($this->scalarArray('regions'), 'is_string'));
    }

    /** @return array<int, string> */
    public function getLanguages(): array
    {
        return array_values(array_filter($this->scalarArray('languages'), 'is_string'));
    }

    /** @return array<int, string> */
    public function getBadges(): array
    {
        return array_values(array_filter($this->scalarArray('badges'), 'is_string'));
    }

    public function getRegionDescription(): ?string
    {
        return $this->str('regionDescription');
    }

    public function getReleaseDateRaw(): ?string
    {
        return $this->str('releaseDate');
    }

    public function getReleaseDateFormatted(string $format = 'd/m/Y'): ?string
    {
        $raw = $this->getReleaseDateRaw();
        if (!$raw) {
            return null;
        }

        $timestamp = strtotime($raw);
        if ($timestamp === false) {
            return null;
        }

        return date($format, $timestamp);
    }
}
