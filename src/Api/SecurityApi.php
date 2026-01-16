<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\SecurityResultItem;

final class SecurityApi
{
    private const SECURITY_ENDPOINT = '/v3/security';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

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
     * @param array $request
     * @return SecurityResultItem|null
     */
    public function check(array $request): ?SecurityResultItem
    {
        $this->validateRequest($request);

        $data = $this->client->requestData(
            'POST',
            self::SECURITY_ENDPOINT,
            $request
        );

        // kontrakt: 200 + {} = chyba / nenalezeno
        if (empty(get_object_vars($data))) {
            return null;
        }

        return new SecurityResultItem($data);
    }

    /**
     * Validate request array.
     */
    private function validateRequest(array $request): void
    {
        if (empty($request['customerEmail']) || empty($request['customerIpAddress'])) {
            throw new \InvalidArgumentException(
                'Security request must contain customerEmail and customerIpAddress.'
            );
        }
    }
}
