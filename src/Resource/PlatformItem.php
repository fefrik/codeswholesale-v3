<?php

namespace CodesWholesaleApi\Resource;

final class PlatformItem extends Resource
{
    public function getName(): ?string
    {
        return $this->str('name');
    }
}
