<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\TerritoryItem;

final class TerritoriesApi
{
    private const TERRITORY_ENDPOINT = '/v3/territory';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all territories.
     *
     * @return array<int, TerritoryItem>
     */
    public function getAll(): array
    {
        $data = $this->client->requestData('GET', self::TERRITORY_ENDPOINT);

        $rows = (isset($data->territories) && is_array($data->territories)) ? $data->territories : [];

        $items = [];
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new TerritoryItem($row);
            }
        }

        return $items;
    }
}
