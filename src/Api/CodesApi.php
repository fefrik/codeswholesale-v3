<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\CodeItem;

final class CodesApi
{
    private const CODES_ENDPOINT = '/v3/codes';

    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get ordered code by codeId.
     *
     * @param string $codeId
     * @return CodeItem|null
     */
    public function getById(string $codeId): ?CodeItem
    {
        $data = $this->client->requestData(
            'GET',
            self::CODES_ENDPOINT . '/' . rawurlencode($codeId)
        );

        if (empty(get_object_vars($data))) {
            return null;
        }

        return new CodeItem($data);
    }
}
