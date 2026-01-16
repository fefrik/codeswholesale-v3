<?php

namespace CodesWholesaleApi\Resource;

final class TerritoryItem extends Resource
{
    public function getTerritory(): ?string
    {
        return $this->str('territory');
    }
}
