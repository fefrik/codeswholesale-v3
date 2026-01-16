<?php

namespace CodesWholesaleApi\Resource;

final class AccountItem extends Resource
{
    /**
     * Get account balance
     */
    public function getBalance(): ?float
    {
        return $this->float('currentBalance');
    }
}
