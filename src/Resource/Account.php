<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

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
        $response = $client->requestData('GET', '/v3/accounts/current');
        return !empty($data) ? new AccountItem($data) : null;
    }
}
