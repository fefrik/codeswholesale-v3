<?php

namespace CodesWholesaleApi\Service;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Code;
use CodesWholesaleApi\Resource\CodeItem;
use CodesWholesaleApi\Resource\Order;
use CodesWholesaleApi\Resource\OrderDetailItem;

class OrderFulfillmentService
{
    /** @var Client */
    private $client;

    /** @var string[] */
    private $readyStatuses;

    /** @var int */
    private $maxStatusPollAttempts;

    /** @var int */
    private $pollSleepSeconds;

    public function __construct(
        Client $client,
        array  $readyStatuses = ['READY', 'PREPARED', 'COMPLETED', 'FULFILLED'],
        int    $maxStatusPollAttempts = 10,
        int    $pollSleepSeconds = 2
    )
    {
        $this->client = $client;
        $this->readyStatuses = $readyStatuses;
        $this->maxStatusPollAttempts = $maxStatusPollAttempts;
        $this->pollSleepSeconds = $pollSleepSeconds;
    }

    /**
     * Create order, wait until it's ready (optional), then return all codes from the order.
     *
     * @param array $orderRequest Body for POST /v3/orders
     * @param bool $waitUntilReady If true, will poll order detail until status is ready or attempts exceeded
     *
     * @return array{order: OrderDetailItem, codes: CodeItem[]}
     */
    public function createAndFetchCodes(array $orderRequest, bool $waitUntilReady = true): array
    {
        $order = Order::create($this->client, $orderRequest);

        if (!$order) {
            throw new \RuntimeException('Order creation returned empty response.');
        }

        // Po create často už dostaneš i codes, ale raději to ověříme přes detail a status.
        if ($waitUntilReady) {
            $order = $this->waitForReadyStatus($order);
        } else {
            $this->assertReadyStatus($order);
        }

        // 1) vyčti codes přímo z detailu (nejrychlejší)
        $codes = $this->extractCodesFromOrder($order);

        // 2) Volitelné: pokud by někdy v detailu code.value nebylo (jen codeId),
        // můžeš si je “dofetchovat” přes /v3/codes/{codeId}. Nechávám jako doplňek:
        $codes = $this->ensureCodeValues($codes);

        return [
            'order' => $order,
            'codes' => $codes,
        ];
    }

    /**
     * Poll order detail until its status is in $readyStatuses.
     */
    private function waitForReadyStatus(OrderDetailItem $order): OrderDetailItem
    {
        $orderId = $order->getOrderId();
        if (!$orderId) {
            throw new \RuntimeException('Order has no orderId.');
        }

        for ($i = 0; $i < $this->maxStatusPollAttempts; $i++) {
            $fresh = Order::getById($this->client, $orderId);
            if (!$fresh) {
                throw new \RuntimeException('Order not found after creation.');
            }

            if ($this->isReadyStatus($fresh->getStatus())) {
                return $fresh;
            }

            sleep($this->pollSleepSeconds);
        }

        throw new \RuntimeException(
            'Order not ready after ' . $this->maxStatusPollAttempts . ' attempts. ' .
            'Last status: ' . (string)$order->getStatus()
        );
    }

    private function assertReadyStatus(OrderDetailItem $order): void
    {
        $status = $order->getStatus();
        if (!$this->isReadyStatus($status)) {
            throw new \RuntimeException('Order status is not ready: ' . (string)$status);
        }
    }

    private function isReadyStatus(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        $s = strtoupper(trim($status));
        foreach ($this->readyStatuses as $allowed) {
            if ($s === strtoupper($allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract codes from order detail: products[] -> codes[]
     *
     * @return CodeItem[]
     */
    private function extractCodesFromOrder(OrderDetailItem $order): array
    {
        $result = [];

        foreach ($order->getProducts() as $product) {
            foreach ($product->getCodes() as $code) {
                $result[] = $code;
            }
        }

        return $result;
    }

    /**
     * If some codes are missing their actual "code" value (only codeId present),
     * fetch full code details via /v3/codes/{codeId}.
     *
     * @param CodeItem[] $codes
     * @return CodeItem[]
     */
    private function ensureCodeValues(array $codes): array
    {
        $out = [];

        foreach ($codes as $c) {
            // pokud už máme value, nech
            if ($c->getCode() !== null && $c->getCode() !== '') {
                $out[] = $c;
                continue;
            }

            $codeId = $c->getCodeId();
            if (!$codeId) {
                $out[] = $c;
                continue;
            }

            $full = Code::getById($this->client, $codeId);
            $out[] = $full ?: $c;
        }

        return $out;
    }
}
