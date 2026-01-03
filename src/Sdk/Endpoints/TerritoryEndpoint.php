<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Territory;
use CodesWholesaleApi\Resource\TerritoryItem;

final class TerritoryEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all territories (typed objects).
     *
     * @return TerritoryItem[]
     */
    public function getAll(): array
    {
        return Territory::getAll($this->client);
    }

    /**
     * Fetch all territories as raw array.
     *
     * @return array
     */
    public function getAllRaw(): array
    {
        return Territory::getAllRaw($this->client);
    }
}
