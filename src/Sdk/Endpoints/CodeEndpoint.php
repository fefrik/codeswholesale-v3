<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Code;
use CodesWholesaleApi\Resource\CodeItem;

final class CodeEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get ordered code by codeId (typed object).
     *
     * @param string $codeId
     * @return CodeItem|null
     */
    public function getById(string $codeId): ?CodeItem
    {
        return Code::getById($this->client, $codeId);
    }

    /**
     * Get ordered code by codeId (raw array).
     *
     * @param string $codeId
     * @return array
     */
    public function getByIdRaw(string $codeId): array
    {
        return Code::getByIdRaw($this->client, $codeId);
    }
}
