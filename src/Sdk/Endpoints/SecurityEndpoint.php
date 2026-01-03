<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Security;
use CodesWholesaleApi\Resource\SecurityResultItem;

final class SecurityEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Check security (typed result).
     *
     * @param array $request
     * @return SecurityResultItem|null
     */
    public function check(array $request): ?SecurityResultItem
    {
        return Security::check($this->client, $request);
    }

    /**
     * Check security (raw array response).
     *
     * @param array $request
     * @return array
     */
    public function checkRaw(array $request): array
    {
        return Security::checkRaw($this->client, $request);
    }
}
