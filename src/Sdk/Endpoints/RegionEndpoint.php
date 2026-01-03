<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Region;

final class RegionEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getAll(): array
    {
        return Region::getAll($this->client);
    }
}
