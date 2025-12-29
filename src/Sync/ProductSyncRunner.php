<?php

namespace CodesWholesaleApi\Sync;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Product;
use CodesWholesaleApi\Resource\ProductItem;
use CodesWholesaleApi\Storage\ContinuationToken\ContinuationTokenStorageInterface;
use CodesWholesaleApi\Storage\LastSync\LastSyncAtStorageInterface;

final class ProductSyncRunner
{
    /** @var Client */
    private $client;

    /** @var ContinuationTokenStorageInterface */
    private $continuationStorage;

    /** @var LastSyncAtStorageInterface */
    private $lastSyncStorage;

    /** @var int */
    private $maxRetry;

    /** @var int */
    private $sleepBetweenRunsSeconds;

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

        // Důležité: "now" si vezmeme na začátku. Po úspěšném dokončení nastavíme lastSyncAt=now.
        // Tím minimalizuješ riziko, že ti změny během běhu utečou mezi "od" a "do".
        $nowUtc = gmdate('c');

        $filters = [
            'updatedSince' => $lastSyncAt,
        ];

        // Konfigurace continuation storage pro Product wrapper
        Product::configureContinuationTokenStorage($this->continuationStorage);

        // Vlastní sync
        Product::getAllWithContinuationStorage(
            $this->client,
            function (array $items) use ($onProduct) {
                foreach ($items as $row) {
                    $onProduct(new ProductItem($row));
                }
            },
            $filters,
            $this->maxRetry
        );

        // Pokud jsme sem došli, sync proběhl celý bez výjimky.
        $this->lastSyncStorage->saveLastSyncAt($nowUtc);

        // continuation token už by měl být smazaný (uložením null),
        // ale pro jistotu:
        $this->continuationStorage->clearToken();

        if ($this->sleepBetweenRunsSeconds > 0) {
            sleep($this->sleepBetweenRunsSeconds);
        }
    }

    /**
     * Run in a loop every $intervalSeconds (simple daemon-style).
     * For cron doporučuji volat runOnce() a nechat schedulovat cronem.
     */
    public function runForever(callable $onProduct, int $intervalSeconds, ?string $initialFallbackIso = null): void
    {
        while (true) {
            $this->runOnce($onProduct, $initialFallbackIso);
            sleep($intervalSeconds);
        }
    }
}
