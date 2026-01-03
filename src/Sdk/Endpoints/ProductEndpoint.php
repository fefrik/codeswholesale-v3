<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Product;
use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Storage\ContinuationToken\ContinuationTokenStorageInterface;

final class ProductEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Configure default continuation token storage (global for Product resource).
     * Useful if you want to call ->getAllWithContinuationStorage() without passing storage each time.
     *
     * @param ContinuationTokenStorageInterface|null $storage
     * @return void
     */
    public function configureContinuationTokenStorage(?ContinuationTokenStorageInterface $storage)
    {
        Product::configureContinuationTokenStorage($storage);
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
     * @return array{items: array, continuationToken: ?string, raw: array}
     */
    public function getPage(array $query = array()): array
    {
        return Product::getPage($this->client, $query);
    }

    /**
     * Retrieve products (paged) with optional filters.
     * This method does NOT persist continuationToken between calls.
     *
     * Callback signature:
     *  function(array $items, ?string $nextToken): void|bool
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
        array    $filters = array(),
        string   $continuationToken = null,
        int      $maxRetry = 5
    ) {
        Product::getAll(
            $this->client,
            $callback,
            $filters,
            $continuationToken,
            $maxRetry
        );
    }

    /**
     * Same as Product::getAllWithContinuationStorage(), but allows passing storage directly.
     *
     * If $storage is provided, it will be configured (globally for Product resource) for this call.
     * If not provided, Product must already have default storage configured.
     *
     * Callback signature:
     *  function(array $items, ?string $nextToken): void|bool
     *
     * @param callable $callback
     * @param array $filters
     * @param int $maxRetry
     * @param ContinuationTokenStorageInterface|null $storage
     * @return void
     */
    public function getAllWithContinuationStorage(
        callable $callback,
        array    $filters = array(),
        int      $maxRetry = 5,
        ContinuationTokenStorageInterface $storage = null
    ) {
        if ($storage !== null) {
            Product::configureContinuationTokenStorage($storage);
        }

        Product::getAllWithContinuationStorage(
            $this->client,
            $callback,
            $filters,
            $maxRetry
        );
    }

    /**
     * Retrieve a single product by its ID.
     *
     * @param string $productId
     * @return ProductItem|null
     */
    public function getById(string $productId): ?ProductItem
    {
        return Product::getById($this->client, $productId);
    }
}
