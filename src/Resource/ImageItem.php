<?php

namespace CodesWholesaleApi\Resource;

class ImageItem extends Resource
{
    public function getFormat(): ?string
    {
        return $this->str('format');
    }

    public function getUrl(): ?string
    {
        return $this->str('image');
    }
}