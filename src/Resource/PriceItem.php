<?php

namespace CodesWholesaleApi\Resource;

class PriceItem extends Resource
{
    public function getFrom(): ?int
    {
        return $this->int('from');
    }

    public function getValue(): ?float
    {
        return $this->float('value');
    }
}
