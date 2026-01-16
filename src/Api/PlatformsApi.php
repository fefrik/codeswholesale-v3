<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\PlatformItem;

final class PlatformsApi
{
    private const PLATFORMS_ENDPOINT = '/v3/platforms';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch platforms list.
     *
     * @return array<int, PlatformItem>
     */
    public function getAll(): array
    {
        $data = $this->client->requestData('GET', self::PLATFORMS_ENDPOINT);

        $rows = (isset($data->platforms) && is_array($data->platforms)) ? $data->platforms : [];

        $items = [];
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new PlatformItem($row);
            }
        }

        return $items;
    }
}
