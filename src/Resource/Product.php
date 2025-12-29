<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\ApiException;

class Product
{
    /**
     * Retrieve products (paged) with optional filters.
     *
     * Supported filters:
     *  - productIds: string|array (CSV string "id1,id2" or ["id1","id2"])
     *  - createdSince: string (ISO-8601), mutually exclusive with updatedSince
     *  - updatedSince: string (ISO-8601), mutually exclusive with createdSince
     *
     * Callback signature:
     *  function(array $items, ?string $nextToken): void|bool
     * Return false to stop early.
     */
    public static function getAll(
        Client   $client,
        callable $callback,
        array    $filters = [],
        ?string  $continuationToken = null,
        int      $maxRetry = 5
    ): void
    {
        self::validateFilters($filters);

        $retry = 0;

        while (true) {
            try {
                $query = self::buildQuery($filters, $continuationToken);

                $response = $client->request('GET', '/v3/products', null, $query);
                $data = $response->getData();

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
                    return;
                }

                usleep(200000);

            } catch (ApiException $e) {
                $retry++;

                if ($retry > $maxRetry) {
                    $status = $e->getResponse()->getStatus();
                    throw new \RuntimeException(
                        "Failed after {$maxRetry} attempts (last HTTP {$status}): {$e->getMessage()}",
                        0,
                        $e
                    );
                }

                sleep(3 * $retry);
            }
        }
    }

    /**
     * Retrieve a single product by its ID.
     *
     * @param Client $client
     * @param string $productId
     *
     * @return ProductItem|null
     */
    public static function getById(Client $client, string $productId): ?ProductItem
    {
        $response = $client->requestData('GET', "/v3/products/{$productId}");
        return !empty($data) ? new ProductItem($data) : null;
    }

    /**
     * Validate filters for mutual exclusivity.
     */
    private static function validateFilters(array $filters): void
    {
        if (!empty($filters['createdSince']) && !empty($filters['updatedSince'])) {
            throw new \InvalidArgumentException('Filters createdSince and updatedSince are mutually exclusive.');
        }
    }

    /**
     * Build query parameters from filters and continuation token.
     */
    private static function buildQuery(array $filters, ?string $continuationToken): array
    {
        $query = [];

        if ($continuationToken) {
            $query['continuationToken'] = $continuationToken;
        }

        if (!empty($filters['productIds'])) {
            // podporuj array i string
            if (is_array($filters['productIds'])) {
                $query['productIds'] = implode(',', $filters['productIds']);
            } else {
                $query['productIds'] = (string)$filters['productIds'];
            }
        }

        if (!empty($filters['createdSince'])) {
            $query['createdSince'] = (string)$filters['createdSince'];
        }

        if (!empty($filters['updatedSince'])) {
            $query['updatedSince'] = (string)$filters['updatedSince'];
        }

        return $query;
    }
}
