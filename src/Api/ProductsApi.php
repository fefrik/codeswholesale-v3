<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Storage\ContinuationToken\ContinuationTokenStorageInterface;

final class ProductsApi
{
    public const PRODUCTS_ENDPOINT = '/v3/products';

    /** @var Client */
    private Client $client;

    /** @var ContinuationTokenStorageInterface|null */
    private ?ContinuationTokenStorageInterface $continuationTokenStorage;

    public function __construct(Client $client, ?ContinuationTokenStorageInterface $continuationTokenStorage = null)
    {
        $this->client = $client;
        $this->continuationTokenStorage = $continuationTokenStorage;
    }

    public function withContinuationTokenStorage(?ContinuationTokenStorageInterface $storage): self
    {
        $clone = clone $this;
        $clone->continuationTokenStorage = $storage;
        return $clone;
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
     * @param array $query
     * @return array{items: array<int, ProductItem>, continuationToken: ?string, raw: \stdClass}
     */
    public function getPage(array $query = []): array
    {
        $this->validateFilters($query);
        $query = $this->normalizeQuery($query);

        $data = $this->client->requestData('GET', self::PRODUCTS_ENDPOINT, null, $query);

        $itemsRaw = (isset($data->items) && is_array($data->items)) ? $data->items : [];
        $items = [];

        foreach ($itemsRaw as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new ProductItem($row);
            }
        }

        $token = (isset($data->continuationToken) && is_string($data->continuationToken))
            ? $data->continuationToken
            : null;

        return [
            'items' => $items,
            'continuationToken' => $token,
            'raw' => $data,
        ];
    }

    /**
     * Retrieve products (paged) with optional filters.
     * This method does NOT remember continuationToken between calls.
     *
     * Callback signature:
     *  function(array<int, ProductItem> $items, ?string $nextToken): void|bool
     * Return false to stop early.
     *
     * @param callable $callback
     * @param array $filters
     * @param string|null $continuationToken
     * @param int $maxRetry
     * @return void
     */
    public function getAll(
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

        $this->validateFilters($filters);

        $retry = 0;

        while (true) {
            try {
                $query = $filters;
                if ($continuationToken) {
                    $query['continuationToken'] = $continuationToken;
                }

                $page = $this->getPage($query);

                $result = call_user_func($callback, $page['items'], $page['continuationToken']);
                if ($result === false) {
                    return;
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
     *  function(array<int, ProductItem> $items, ?string $nextToken): void|bool
     * Return false to stop early (token is still saved for the last processed page).
     */
    public function getAllWithContinuationStorage(
        callable $callback,
        array $filters = [],
        int $maxRetry = 5
    ): void {
        if (!$this->continuationTokenStorage) {
            throw new \LogicException(
                'ContinuationTokenStorage is not configured. Pass it to constructor or use withContinuationTokenStorage().'
            );
        }

        $storage = $this->continuationTokenStorage;
        $startToken = $storage->getToken();

        $this->getAll(
            function (array $items, ?string $nextToken) use ($callback, $storage) {
                $result = call_user_func($callback, $items, $nextToken);

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
    public function getById(string $productId): ?ProductItem
    {
        $data = $this->client->requestData('GET', self::PRODUCTS_ENDPOINT . '/' . $productId);

        if (empty(get_object_vars($data))) {
            return null;
        }

        return new ProductItem($data);
    }

    private function validateFilters(array $filters): void
    {
        if (!empty($filters['createdSince']) && !empty($filters['updatedSince'])) {
            throw new \InvalidArgumentException('Filters createdSince and updatedSince are mutually exclusive.');
        }
    }

    private function normalizeQuery(array $query): array
    {
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
