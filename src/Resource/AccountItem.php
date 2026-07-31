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

    public function getCurrentCredit(): ?float { return $this->float('currentCredit'); }
    public function getEmail(): ?string { return $this->str('email'); }
    public function getFullName(): ?string { return $this->str('fullName'); }
    public function getTotalToUse(): ?float { return $this->float('totalToUse'); }

    /** @return list<LinkItem> */
    public function getLinks(): array { return iterator_to_array($this->iterateLinks(), false); }

    /** @return \Generator<int, LinkItem, void, void> */
    public function iterateLinks(): \Generator
    {
        foreach ($this->iterateObjects('links') as $item) yield new LinkItem($item);
    }
}
