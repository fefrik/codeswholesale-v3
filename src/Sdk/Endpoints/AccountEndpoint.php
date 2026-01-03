<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Account;
use CodesWholesaleApi\Resource\AccountItem;

final class AccountEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve current account details.
     *
     * @return AccountItem|null
     */
    public function getCurrent(): ?AccountItem
    {
        return Account::getCurrent($this->client);
    }
}
