<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Security
{
    /**
     * Check security.
     *
     * Body example:
     *  {
     *    'customerEmail' => '...',
     *    'customerIpAddress' => '...',
     *    'customerPaymentEmail' => '...',
     *    'customerUserAgent' => '...'
     *  }
     *
     * @param Client $client
     * @param array $request
     * @return SecurityResultItem|null
     */
    public static function check(Client $client, array $request): ?SecurityResultItem
    {
        self::validateRequest($request);
        $data = $client->requestData('POST', '/v3/security', $request);
        return !empty($data) ? new SecurityResultItem($data) : null;
    }

    /**
     * Raw variant.
     *
     * @param Client $client
     * @param array $request
     * @return array
     */
    public static function checkRaw(Client $client, array $request): array
    {
        self::validateRequest($request);
        return $client->requestData('POST', '/v3/security', $request);
    }

    /**
     * Validate request array.
     *
     * @param array $request
     * @return void
     */
    private static function validateRequest(array $request): void
    {
        // customerEmail a customerIpAddress are required
        if (empty($request['customerEmail']) || empty($request['customerIpAddress'])) {
            throw new \InvalidArgumentException('Security request must contain customerEmail and customerIpAddress.');
        }
    }
}
