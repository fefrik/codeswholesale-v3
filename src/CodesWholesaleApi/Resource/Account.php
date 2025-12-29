<?php

namespace CodesWholesaleApi\Resource;
use CodesWholesaleApi\Api\Client;

class Account {

    /**
     * Retrieve current account details
     *
     * @param Client $client
     * @return AccountItem|null
     * @throws \Exception
     */
    public static function getCurrent(Client $client): ?AccountItem {
        $data = $client->request('GET', '/v3/accounts/current');
        return $data ? new AccountItem($data) : null;
    }
}