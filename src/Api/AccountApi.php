<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\AccountItem;

final class AccountApi
{
    const ENDPOINT = '/v3/accounts/current';

    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve current account details
     */
    public function getCurrent(): ?AccountItem
    {
        $data = $this->client->requestData('GET', self::ENDPOINT);

        if (empty(get_object_vars($data))) {
            return null;
        }

        return new AccountItem($data);
    }


}
