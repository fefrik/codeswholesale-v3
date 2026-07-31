<?php

namespace CodesWholesaleApi\Resource;

final class LocalizedTitleItem extends Resource
{
    public function getTerritory(): ?string { return $this->str('territory'); }
    public function getTitle(): ?string { return $this->str('title'); }
}
