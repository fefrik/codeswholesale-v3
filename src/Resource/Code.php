<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Code
{
    /**
     * Get ordered code by codeId.
     *
     * @param Client $client
     * @param string $codeId
     * @return CodeItem|null
     */
    public static function getById(Client $client, string $codeId): ?CodeItem
    {
        $data = $client->requestData('GET', '/v3/codes/' . rawurlencode($codeId));
        return !empty($data) ? new CodeItem($data) : null;
    }

    /**
     * Get ordered code by codeId (raw).
     *
     * @param Client $client
     * @param string $codeId
     * @return array
     */
    public static function getByIdRaw(Client $client, string $codeId): array
    {
        return $client->requestData('GET', '/v3/codes/' . rawurlencode($codeId));
    }
}
