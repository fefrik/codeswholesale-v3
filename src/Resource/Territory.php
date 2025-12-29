<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Territory
{
    /**
     * Fetch all territories.
     *
     * @param Client $client
     * @return TerritoryItem[]
     */
    public static function getAll(Client $client): array
    {
        $data = $client->requestData('GET', '/v3/territory');

        $rows = isset($data['territories']) && is_array($data['territories'])
            ? $data['territories']
            : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new TerritoryItem($row);
            }
        }

        return $items;
    }

    /**
     * Fetch all territories as raw array.
     *
     * @param Client $client
     * @return array
     */
    public static function getAllRaw(Client $client): array
    {
        return $client->requestData('GET', '/v3/territory');
    }
}
