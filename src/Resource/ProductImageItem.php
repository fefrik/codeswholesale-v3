<?php

namespace CodesWholesaleApi\Resource;

final class ProductImageItem extends Resource
{
    public function getId(): ?string
    {
        return $this->str('id');
    }

    public function getImage(): ?string
    {
        return $this->str('image');
    }

    public function getUrl(): ?string
    {
        return $this->getImage();
    }

    public function getFormat(): ?string
    {
        return $this->str('format');
    }
}
