<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\ApiException;

class Product
{
    /**
     * Retrieve all products (paged), optionally processing them with a callback.
     *
     * Callback signature:
     *   function(array $items, ?string $nextToken): void|bool
     * If callback returns false, iteration stops early.
     *
     * @param Client $client
     * @param callable $callback
     * @param string|null $continuationToken
     * @param int $maxRetry
     *
     * @return void
     */
    public static function getAll(
        Client $client,
        callable $callback,
        ?string $continuationToken = null,
        int $maxRetry = 5
    ): void {
        $retry = 0;

        while (true) {
            try {
                $query = [];
                if ($continuationToken) {
                    $query['continuationToken'] = $continuationToken;
                }

                $apiResponse = $client->request('GET', '/v3/products', null, $query);
                $data = $apiResponse->getData();

                $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
                $nextToken = isset($data['continuationToken']) && is_string($data['continuationToken'])
                    ? $data['continuationToken']
                    : null;

                if (!empty($items)) {
                    $result = $callback($items, $nextToken);
                    if ($result === false) {
                        return;
                    }
                }

                $continuationToken = $nextToken;
                $retry = 0;

                if (!$continuationToken) {
                    return; // done
                }

                // malý “polite delay” vůči API
                usleep(200000);
            } catch (ApiException $e) {
                $retry++;
                if ($retry > $maxRetry) {
                    $status = $e->getResponse()->getStatus();
                    throw new \RuntimeException(
                        "Failed after {$maxRetry} attempts (last HTTP {$status}): " . $e->getMessage(),
                        0,
                        $e
                    );
                }
                sleep(3 * $retry);
            }
        }
    }

    public static function getById(Client $client, string $productId): ?ProductItem
    {
        $response = $client->requestData('GET', "/v3/products/{$productId}");
        return !empty($data) ? new ProductItem($data) : null;
    }
}
