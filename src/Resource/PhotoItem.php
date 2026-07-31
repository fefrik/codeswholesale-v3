<?php

namespace CodesWholesaleApi\Resource;

final class PhotoItem extends Resource
{
    public function getTerritory(): ?string { return $this->str('territory'); }
    public function getType(): ?string { return $this->str('type'); }
    public function getUrl(): ?string { return $this->str('url'); }
}
