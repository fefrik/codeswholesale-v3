<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Security
{
    /**
     * Check security.
     *
     * Body example:
     *  [
     *    'customerEmail' => '...',
     *    'customerIpAddress' => '...',
     *    'customerPaymentEmail' => '...',
     *    'customerUserAgent' => '...'
     *  ]
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

    private static function validateRequest(array $request): void
    {
        // Nevíme co je povinné, ale minimálně hlídáme, že nepředáváš úplně prázdné.
        if (empty($request)) {
            throw new \InvalidArgumentException('Security request body must not be empty.');
        }

        // Volitelně můžeš zpřísnit:
        // $allowed = ['customerEmail','customerIpAddress','customerPaymentEmail','customerUserAgent'];
        // foreach ($request as $k => $v) { if (!in_array($k, $allowed, true)) unset($request[$k]); }
    }
}
