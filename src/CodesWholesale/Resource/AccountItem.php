<?php

namespace CodesWholesale\Resource;

final class AccountItem
{
    /** @var array */
    private $data;

    /**
     * @param array $data Account data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get account balance
     */
    public function getBalance(): ?float
    {
        return isset($this->data['currentBalance'])
            ? (float)$this->data['currentBalance']
            : null;
    }

    /**
     * Raw API payload (debug / advanced usage)
     */
    public function getRaw(): array
    {
        return $this->data;
    }
}
