<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\ApiResponse;

class Account
{
    /**
     * Retrieve current account details
     *
     * @param Client $client
     * @return AccountItem|null
     */
    public static function getCurrent(Client $client): ?AccountItem
    {
        $response = $client->request('GET', '/v3/accounts/current');
        $data = $response->getData();

        return !empty($data) ? new AccountItem($data) : null;
    }
}
