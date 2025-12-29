<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Platform
{
    /**
     * Fetch platforms list.
     *
     * @param Client $client
     * @return PlatformItem[]  List of PlatformItem
     */
    public static function getAll(Client $client): array
    {
        $data = $client->requestData('GET', '/v3/platforms');

        $platforms = isset($data['platforms']) && is_array($data['platforms'])
            ? $data['platforms']
            : [];

        $items = [];
        foreach ($platforms as $row) {
            if (is_array($row)) {
                $items[] = new PlatformItem($row);
            }
        }

        return $items;
    }

    /**
     * Fetch platforms as raw array.
     *
     * @param Client $client
     * @return array
     */
    public static function getRaw(Client $client): array
    {
        return $client->requestData('GET', '/v3/platforms');
    }
}
