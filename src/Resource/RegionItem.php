<?php

namespace CodesWholesaleApi\Resource;

final class RegionItem extends Resource
{
    public function getName(): ?string
    {
        return $this->str('name');
    }
}
