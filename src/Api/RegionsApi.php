<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\RegionItem;

final class RegionsApi
{
    private const REGIONS_ENDPOINT = '/v3/regions';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all regions.
     *
     * @return array<int, RegionItem>
     */
    public function getAll(): array
    {
        $data = $this->client->requestData('GET', self::REGIONS_ENDPOINT);

        $rows = (isset($data->regions) && is_array($data->regions)) ? $data->regions : [];

        $items = [];
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new RegionItem($row);
            }
        }

        return $items;
    }
}
