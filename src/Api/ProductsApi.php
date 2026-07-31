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
        foreach ($this->iteratePages($filters, $continuationToken, $maxRetry) as $page) {
            $result = $callback($page['items'], $page['continuationToken']);
            if ($result === false) {
                return;
            }
        }
    }

    /**
     * Stream products one by one while keeping only the current API page in memory.
     *
     * @return \Generator<int, ProductItem, void, void>
     */
    public function iterate(
        array $filters = [],
        ?string $continuationToken = null,
        int $maxRetry = 5
    ): \Generator {
        foreach ($this->iteratePages($filters, $continuationToken, $maxRetry) as $page) {
            foreach ($page['items'] as $product) {
                yield $product;
            }
        }
    }

    /**
     * Stream products and checkpoint only after the whole current page was consumed.
     * If iteration is interrupted mid-page, that page is safely repeated on resume.
     *
     * @return \Generator<int, ProductItem, void, void>
     */
    public function iterateWithContinuationStorage(
        array $filters = [],
        int $maxRetry = 5
    ): \Generator {
        if (!$this->continuationTokenStorage) {
            throw new \LogicException(
                'ContinuationTokenStorage is not configured. Pass it to constructor or use withContinuationTokenStorage().'
            );
        }

        $storage = $this->continuationTokenStorage;
        foreach ($this->iteratePages($filters, $storage->getToken(), $maxRetry) as $page) {
            foreach ($page['items'] as $product) {
                yield $product;
            }

            $storage->saveToken($page['continuationToken']);
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
        if ($productId === '') {
            throw new \InvalidArgumentException('productId must not be empty.');
        }

        $data = $this->client->requestData('GET', self::PRODUCTS_ENDPOINT . '/' . rawurlencode($productId));

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

    /**
     * @return \Generator<int, array{items: array<int, ProductItem>, continuationToken: ?string, raw: \stdClass}, void, void>
     */
    private function iteratePages(
        array $filters,
        ?string $continuationToken,
        int $maxRetry
    ): \Generator {
        if (isset($filters['continuationToken'])) {
            throw new \InvalidArgumentException(
                'continuationToken does not belong to filters; pass it as a separate argument.'
            );
        }
        if ($maxRetry < 0) {
            throw new \InvalidArgumentException('maxRetry must not be negative.');
        }

        $this->validateFilters($filters);

        while (true) {
            $query = $filters;
            if ($continuationToken !== null && $continuationToken !== '') {
                $query['continuationToken'] = $continuationToken;
            }

            $page = $this->getPageWithRetry($query, $maxRetry);
            yield $page;

            $continuationToken = $page['continuationToken'];
            if ($continuationToken === null || $continuationToken === '') {
                return;
            }

            usleep(200000);
        }
    }

    /** @return array{items: array<int, ProductItem>, continuationToken: ?string, raw: \stdClass} */
    private function getPageWithRetry(array $query, int $maxRetry): array
    {
        $retry = 0;

        while (true) {
            try {
                return $this->getPage($query);
            } catch (ApiException $e) {
                $status = $e->getResponse()->getStatus();
                if ($status !== 429 && $status < 500) {
                    throw $e;
                }

                if ($retry >= $maxRetry) {
                    throw new \RuntimeException(
                        "Failed after {$maxRetry} retries (last HTTP {$status}): {$e->getMessage()}",
                        0,
                        $e
                    );
                }

                $retry++;
                sleep(3 * $retry);
            }
        }
    }
}
