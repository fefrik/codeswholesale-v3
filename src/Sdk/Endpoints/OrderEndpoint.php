<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Order;
use CodesWholesaleApi\Resource\OrderDetailItem;
use CodesWholesaleApi\Resource\OrderItem;

final class OrderEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch single order by orderId (detail, typed).
     *
     * @param string $orderId
     * @return OrderDetailItem|null
     */
    public function getById(string $orderId): ?OrderDetailItem
    {
        return Order::getById($this->client, $orderId);
    }

    /**
     * Fetch single order by orderId (raw).
     *
     * @param string $orderId
     * @return array
     */
    public function getByIdRaw(string $orderId): array
    {
        return Order::getByIdRaw($this->client, $orderId);
    }

    /**
     * Get order history (typed list).
     *
     * @param string|null $startFrom ISO-8601 string (recommended)
     * @param string|null $endOn ISO-8601 string (recommended)
     * @return OrderItem[]
     */
    public function getAll(?string $startFrom = null, ?string $endOn = null): array
    {
        return Order::getAll($this->client, $startFrom, $endOn);
    }

    /**
     * Get order history (raw).
     *
     * @param string|null $startFrom
     * @param string|null $endOn
     * @return array
     */
    public function getAllRaw(?string $startFrom = null, ?string $endOn = null): array
    {
        return Order::getAllRaw($this->client, $startFrom, $endOn);
    }

    /**
     * Create order (typed detail result).
     *
     * @param array $request
     * @return OrderDetailItem|null
     */
    public function create(array $request): ?OrderDetailItem
    {
        return Order::create($this->client, $request);
    }

    /**
     * Create order (raw).
     *
     * @param array $request
     * @return array
     */
    public function createRaw(array $request): array
    {
        return Order::createRaw($this->client, $request);
    }
}
