<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\ApiException;
use CodesWholesaleApi\Storage\ContinuationToken\ContinuationTokenStorageInterface;

class Product
{
    /** @var ContinuationTokenStorageInterface|null */
    private static ?ContinuationTokenStorageInterface $defaultContinuationTokenStorage = null;

    /**
     * Configure default continuation token storage for Product pagination.
     */
    public static function configureContinuationTokenStorage(
        ?ContinuationTokenStorageInterface $storage
    ): void {
        self::$defaultContinuationTokenStorage = $storage;
    }

    /**
     * Fetch exactly one page of products.
     *
     * Supported query keys:
     *  - productIds: string|array (CSV "id1,id2" or ["id1","id2"])
     *  - createdSince: string (ISO-8601) mutually exclusive with updatedSince
     *  - updatedSince: string (ISO-8601) mutually exclusive with createdSince
     *  - continuationToken: string
     *
     * @return array{items: array, continuationToken: ?string, raw: array}
     */
    public static function getPage(Client $client, array $query = []): array
    {
        self::validateFilters($query);
        $query = self::normalizeQuery($query);

        $data = $client->requestData('GET', '/v3/products', null, $query);

        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
        $token = isset($data['continuationToken']) && is_string($data['continuationToken'])
            ? $data['continuationToken']
            : null;

        return [
            'items' => $items,
            'continuationToken' => $token,
            'raw' => $data,
        ];
    }

    /**
     * Retrieve products (paged) with optional filters.
     * This method don't remember continuationToken between calls.
     *
     * Callback signature:
     *  function(array $items, ?string $nextToken): void|bool
     * Return false to stop early.
     */
    public static function getAll(
        Client $client,
        callable $callback,
        array $filters = [],
        ?string $continuationToken = null,
        int $maxRetry = 5
    ): void {
        if (isset($filters['continuationToken'])) {
            throw new \InvalidArgumentException(
                'continuationToken does not belong to filters; pass it as a separate argument.'
            );
        }

        self::validateFilters($filters);

        $retry = 0;

        while (true) {
            try {
                $query = $filters;

                if ($continuationToken) {
                    $query['continuationToken'] = $continuationToken;
                }

                $page = self::getPage($client, $query);

                if (!empty($page['items'])) {
                    $result = $callback($page['items'], $page['continuationToken']);
                    if ($result === false) {
                        return;
                    }
                }

                $continuationToken = $page['continuationToken'];
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
     * Wrapper around getAll() that persists continuationToken after each processed page.
     *
     * Callback signature:
     *  function(array $items, ?string $nextToken): void|bool
     * Return false to stop early (token is still saved for the last processed page).
     *
     * @param Client $client
     * @param callable $callback
     * @param array $filters
     * @param int $maxRetry
     * @return void
     */
    public static function getAllWithContinuationStorage(
        Client $client,
        callable $callback,
        array $filters = [],
        int $maxRetry = 5
    ): void {
        if (!self::$defaultContinuationTokenStorage) {
            throw new \LogicException(
                'ContinuationTokenStorage is not configured. ' .
                'Call Product::configureContinuationTokenStorage() first.'
            );
        }

        $storage = self::$defaultContinuationTokenStorage;
        $startToken = $storage->getToken();

        self::getAll(
            $client,
            function (array $items, ?string $nextToken) use ($callback, $storage) {
                $result = $callback($items, $nextToken);

                // checkpoint after successful processing
                $storage->saveToken($nextToken);

                return $result;
            },
            $filters,
            $startToken,
            $maxRetry
        );
    }


    /**
     * Retrieve a single product by its ID.
     */
    public static function getById(Client $client, string $productId): ?ProductItem
    {
        $data = $client->requestData('GET', "/v3/products/{$productId}");
        return !empty($data) ? new ProductItem($data) : null;
    }

    private static function validateFilters(array $filters): void
    {
        if (!empty($filters['createdSince']) && !empty($filters['updatedSince'])) {
            throw new \InvalidArgumentException('Filters createdSince and updatedSince are mutually exclusive.');
        }
    }

    private static function normalizeQuery(array $query): array
    {
        // productIds: pole -> CSV, protože http_build_query by jinak dělalo productIds[0]=...
        if (!empty($query['productIds']) && is_array($query['productIds'])) {
            $query['productIds'] = implode(',', $query['productIds']);
        }

        // uklid prázdných stringů
        foreach (['productIds', 'createdSince', 'updatedSince', 'continuationToken'] as $k) {
            if (isset($query[$k]) && $query[$k] === '') {
                unset($query[$k]);
            }
        }

        return $query;
    }
}
