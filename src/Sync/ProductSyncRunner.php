<?php

namespace CodesWholesaleApi\Sync;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\ProductsApi;
use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Storage\ContinuationToken\ContinuationTokenStorageInterface;
use CodesWholesaleApi\Storage\LastSync\LastSyncAtStorageInterface;

final class ProductSyncRunner
{
    private Client $client;
    private ContinuationTokenStorageInterface $continuationStorage;
    private LastSyncAtStorageInterface $lastSyncStorage;
    private int $maxRetry;
    private int $sleepBetweenRunsSeconds;

    public function __construct(
        Client $client,
        ContinuationTokenStorageInterface $continuationStorage,
        LastSyncAtStorageInterface $lastSyncStorage,
        int $maxRetry = 5,
        int $sleepBetweenRunsSeconds = 0
    ) {
        $this->client = $client;
        $this->continuationStorage = $continuationStorage;
        $this->lastSyncStorage = $lastSyncStorage;
        $this->maxRetry = $maxRetry;
        $this->sleepBetweenRunsSeconds = $sleepBetweenRunsSeconds;
    }

    /**
     * Run one sync pass:
     * - updatedSince = lastSyncAt (or fallback)
     * - resume using continuationToken
     * - save continuationToken after each page
     * - when completed successfully, update lastSyncAt = now (UTC) and clear continuation token
     *
     * @param callable $onProduct function(ProductItem $product): void
     * @param string|null $initialFallbackIso If lastSyncAt is empty, use this; otherwise defaults to "1970-01-01T00:00:00Z"
     */
    public function runOnce(callable $onProduct, ?string $initialFallbackIso = null): void
    {
        $lastSyncAt = $this->lastSyncStorage->getLastSyncAt();
        if (!$lastSyncAt) {
            $lastSyncAt = $initialFallbackIso ?: '1970-01-01T00:00:00Z';
        }

        // "now" bereme na zacatku, po uspechu nastavime lastSyncAt=now
        $nowUtc = gmdate('c');

        $filters = [
            'updatedSince' => $lastSyncAt,
        ];

        $productsApi = (new ProductsApi($this->client))
            ->withContinuationTokenStorage($this->continuationStorage);

        foreach ($productsApi->iterateWithContinuationStorage($filters, $this->maxRetry) as $product) {
            $onProduct($product);
        }

        // sync dobehl bez vyjimky -> checkpoint
        $this->lastSyncStorage->saveLastSyncAt($nowUtc);

        // The streaming iterator stores null after its final page; keep completion explicit.
        $this->continuationStorage->clearToken();

        if ($this->sleepBetweenRunsSeconds > 0) {
            sleep($this->sleepBetweenRunsSeconds);
        }
    }

    public function runForever(callable $onProduct, int $intervalSeconds, ?string $initialFallbackIso = null): void
    {
        if (PHP_SAPI !== 'cli') {
            throw new \RuntimeException('runForever() must be executed in CLI (worker) mode.');
        }

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        while (true) {
            $this->runOnce($onProduct, $initialFallbackIso);
            sleep($intervalSeconds);
        }
    }

    public function runForSeconds(callable $onProduct, int $seconds, int $intervalSeconds = 0, ?string $initialFallbackIso = null): void
    {
        $deadline = time() + $seconds;

        while (time() < $deadline) {
            $this->runOnce($onProduct, $initialFallbackIso);

            if ($intervalSeconds > 0 && time() < $deadline) {
                sleep($intervalSeconds);
            }
        }
    }
}
