<?php

namespace CodesWholesaleApi\Service;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\OrdersApi;
use CodesWholesaleApi\Api\CodesApi;
use CodesWholesaleApi\Resource\CodeItem;
use CodesWholesaleApi\Resource\OrderDetailItem;

final class OrderFulfillmentService
{
    private Client $client;

    /** @var array<int, string> */
    private array $readyStatuses;

    private int $maxStatusPollAttempts;
    private int $pollSleepSeconds;

    private OrdersApi $ordersApi;
    private CodesApi $codesApi;

    public function __construct(
        Client $client,
        array $readyStatuses = ['READY', 'PREPARED', 'COMPLETED', 'FULFILLED'],
        int $maxStatusPollAttempts = 10,
        int $pollSleepSeconds = 2
    ) {
        $this->client = $client;
        $this->readyStatuses = $readyStatuses;
        $this->maxStatusPollAttempts = $maxStatusPollAttempts;
        $this->pollSleepSeconds = $pollSleepSeconds;

        // můžeš i injectnout zvenku, ale tohle je nejmenší změna
        $this->ordersApi = new OrdersApi($client);
        $this->codesApi = new CodesApi($client);
    }

    /**
     * Create order, wait until it's ready (optional), then return all codes from the order.
     *
     * @param array $orderRequest Body for POST /v3/orders
     * @param bool $waitUntilReady If true, will poll order detail until status is ready or attempts exceeded
     *
     * @return array{order: OrderDetailItem, codes: array<int, CodeItem>}
     */
    public function createAndFetchCodes(array $orderRequest, bool $waitUntilReady = true): array
    {
        $order = $this->ordersApi->create($orderRequest);

        // Po create často už dostaneš i codes, ale raději to ověříme přes detail a status.
        if ($waitUntilReady) {
            $order = $this->waitForReadyStatus($order);
        } else {
            $this->assertReadyStatus($order);
        }

        // 1) vyčti codes přímo z detailu (nejrychlejší)
        $codes = $this->extractCodesFromOrder($order);

        // 2) Volitelné: pokud by někdy v detailu code.value nebylo (jen codeId),
        // dofetch přes /v3/codes/{codeId}
        $codes = $this->ensureCodeValues($codes);

        return [
            'order' => $order,
            'codes' => $codes,
        ];
    }

    private function waitForReadyStatus(OrderDetailItem $order): OrderDetailItem
    {
        $orderId = $order->getOrderId();
        if (!$orderId) {
            throw new \RuntimeException('Order has no orderId.');
        }

        $lastStatus = $order->getStatus();

        for ($i = 0; $i < $this->maxStatusPollAttempts; $i++) {
            $fresh = $this->ordersApi->getById($orderId);
            $lastStatus = $fresh->getStatus();

            if ($this->isReadyStatus($lastStatus)) {
                return $fresh;
            }

            sleep($this->pollSleepSeconds);
        }

        throw new \RuntimeException(
            'Order not ready after ' . $this->maxStatusPollAttempts . ' attempts. ' .
            'Last status: ' . (string) $lastStatus
        );
    }

    private function assertReadyStatus(OrderDetailItem $order): void
    {
        $status = $order->getStatus();
        if (!$this->isReadyStatus($status)) {
            throw new \RuntimeException('Order status is not ready: ' . (string) $status);
        }
    }

    private function isReadyStatus(?string $status): bool
    {
        if ($status === null) {
            return false;
        }

        $s = strtoupper(trim($status));
        foreach ($this->readyStatuses as $allowed) {
            if ($s === strtoupper((string) $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract codes from order detail: products[] -> codes[]
     *
     * @return array<int, CodeItem>
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
     * @param array<int, CodeItem> $codes
     * @return array<int, CodeItem>
     */
    private function ensureCodeValues(array $codes): array
    {
        $out = [];

        foreach ($codes as $c) {
            if ($c->getCode() !== null && $c->getCode() !== '') {
                $out[] = $c;
                continue;
            }

            $codeId = $c->getCodeId();
            if (!$codeId) {
                $out[] = $c;
                continue;
            }

            // getById už typicky vyhazuje exception při 404,
            // ale pro jistotu necháme fallback na původní $c
            try {
                $out[] = $this->codesApi->getById($codeId);
            } catch (\Throwable $e) {
                $out[] = $c;
            }
        }

        return $out;
    }
}
