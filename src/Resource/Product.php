<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Product {

    /**
     * Retrieve all products, optionally processing them with a callback
     *
     * @param Client $client The CodesWholesale client
     * @param callable|null $callback Callback to process retrieved items
     * @param string|null $continuationToken Token for paginated requests
     *
     * @return void
     * @throws \Exception If maximum retries are exceeded
     */
    public static function getAll(Client $client, callable $callback, ?string $continuationToken = null): void {

        $retry = 0;
        $maxRetry = 5;

        do {

            try {

                $params = [];

                if ($continuationToken) {
                    $params['continuationToken'] = $continuationToken;
                }

                $response = $client->request('GET', '/v3/products', $params);

                if (!empty($response['items'])) {
                    $callback($response['items'], $response['continuationToken'] ?? null);
                }

                $continuationToken = $response['continuationToken'] ?? null;
                $retry = 0;

                usleep(200000);

            } catch (\Exception $e) {

                $retry++;

                if ($retry > $maxRetry) {
                    throw new \Exception("Failed after {$maxRetry} attempts: " . $e->getMessage());
                }

                sleep(3);

            }

        } while ($continuationToken);

    }


    /**
     * Retrieve a product by its ID
     *
     * @param Client $client The CodesWholesale client
     * @param string $productId The product identifier
     *
     * @return ProductItem|null ProductItem instance or null if not found
     */
    public static function getById(Client $client, string $productId): ?ProductItem {

        $response = $client->request('GET', "/v3/products/{$productId}");
        $data = $response->getData();

        return !empty($data) ? new ProductItem($data) : null;

    }

}