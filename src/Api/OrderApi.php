<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\OrderDetailItem;
use CodesWholesaleApi\Resource\OrderItem;

final class OrdersApi
{
    private const ORDERS_ENDPOINT = '/v3/orders';
    private const ORDER_DETAIL_ENDPOINT = '/v3/orders/%s';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch single order by orderId (detail).
     */
    public function getById(string $orderId): ?OrderDetailItem
    {
        $path = sprintf(self::ORDER_DETAIL_ENDPOINT, rawurlencode($orderId));
        $data = $this->client->requestData('GET', $path);

        if (empty(get_object_vars($data))) {
            return null;
        }

        return new OrderDetailItem($data);
    }

    /**
     * Get order history.
     *
     * @return array<int, OrderItem>
     */
    public function getAll(?string $startFrom = null, ?string $endOn = null): array
    {
        $query = [];

        if ($startFrom !== null && $startFrom !== '') {
            $query['startFrom'] = $startFrom;
        }
        if ($endOn !== null && $endOn !== '') {
            $query['endOn'] = $endOn;
        }

        $data = $this->client->requestData('GET', self::ORDERS_ENDPOINT, null, $query);

        $rows = (isset($data->items) && is_array($data->items)) ? $data->items : [];

        $items = [];
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new OrderItem($row);
            }
        }

        return $items;
    }

    /**
     * Create order.
     *
     * Body example:
     * [
     *   'allowPreOrder' => true,
     *   'orderId' => 'your-client-order-id-or-external-id',
     *   'products' => [
     *     ['price' => 10.5, 'productId' => 'XXX', 'quantity' => 1],
     *   ]
     * ]
     */
    public function create(array $request): ?OrderDetailItem
    {
        $this->validateCreateRequest($request);

        $data = $this->client->requestData('POST', self::ORDERS_ENDPOINT, $request);

        if (empty(get_object_vars($data))) {
            return null;
        }

        return new OrderDetailItem($data);
    }

    private function validateCreateRequest(array $request): void
    {
        if (empty($request)) {
            throw new \InvalidArgumentException('Order request body must not be empty.');
        }

        if (!isset($request['products']) || !is_array($request['products']) || count($request['products']) === 0) {
            throw new \InvalidArgumentException('Order request must contain non-empty "products" array.');
        }

        foreach ($request['products'] as $i => $p) {
            if (!is_array($p)) {
                throw new \InvalidArgumentException("Order products[{$i}] must be an object/array.");
            }
            if (empty($p['productId'])) {
                throw new \InvalidArgumentException("Order products[{$i}].productId is required.");
            }
            if (!isset($p['quantity'])) {
                throw new \InvalidArgumentException("Order products[{$i}].quantity is required.");
            }
        }

        if (isset($request['allowPreOrder']) && !is_bool($request['allowPreOrder'])) {
            throw new \InvalidArgumentException('Order request allowPreOrder must be boolean.');
        }
    }
}
