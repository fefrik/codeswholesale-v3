<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Region
{
    /**
     * Fetch all regions.
     *
     * @param Client $client
     * @return RegionItem[]
     */
    public static function getAll(Client $client): array
    {
        $data = $client->requestData('GET', '/v3/regions');

        $rows = isset($data['regions']) && is_array($data['regions'])
            ? $data['regions']
            : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new RegionItem($row);
            }
        }

        return $items;
    }

    /**
     * Fetch all regions as raw array.
     *
     * @param Client $client
     * @return array
     */
    public static function getAllRaw(Client $client): array
    {
        return $client->requestData('GET', '/v3/regions');
    }
}
