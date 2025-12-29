<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Order
{

    /**
     * Fetch single order by orderId (detail).
     *
     * @param Client $client
     * @param string $orderId
     * @return OrderDetailItem|null
     */
    public static function getById(Client $client, string $orderId): ?OrderDetailItem
    {
        $data = self::getByIdRaw($client, $orderId);
        return !empty($data) ? new OrderDetailItem($data) : null;
    }

    /**
     * Fetch single order by orderId (raw).
     *
     * @param Client $client
     * @param string $orderId
     * @return array
     */
    public static function getByIdRaw(Client $client, string $orderId): array
    {
        return $client->requestData('GET', '/v3/orders/' . rawurlencode($orderId));
    }

    /**
     * Get order history.
     *
     * @param Client $client
     * @param string|null $startFrom Start date (string, ideally ISO-8601)
     * @param string|null $endOn End date (string, ideally ISO-8601)
     * @return OrderItem[]
     */
    public static function getAll(Client $client, ?string $startFrom = null, ?string $endOn = null): array
    {
        $data = self::getAllRaw($client, $startFrom, $endOn);

        $rows = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new OrderItem($row);
            }
        }

        return $items;
    }

    /**
     * Get order history (raw).
     *
     * @param Client $client
     * @param string|null $startFrom
     * @param string|null $endOn
     * @return array
     */
    public static function getAllRaw(Client $client, ?string $startFrom = null, ?string $endOn = null): array
    {
        $query = [];

        if ($startFrom !== null && $startFrom !== '') {
            $query['startFrom'] = $startFrom;
        }
        if ($endOn !== null && $endOn !== '') {
            $query['endOn'] = $endOn;
        }

        return $client->requestData('GET', '/v3/orders', null, $query);
    }

    /**
     * Create order.
     *
     * Body example:
     * [
     *   'allowPreOrder' => true,
     *   'orderId' => 'your-client-order-id-or-external-id', // podle API významu
     *   'products' => [
     *     ['price' => 10.5, 'productId' => 'XXX', 'quantity' => 1],
     *   ]
     * ]
     *
     * @param Client $client
     * @param array $request
     * @return OrderDetailItem|null
     */
    public static function create(Client $client, array $request): ?OrderDetailItem
    {
        self::validateCreateRequest($request);

        $data = $client->requestData('POST', '/v3/orders', $request);

        return !empty($data) ? new OrderDetailItem($data) : null;
    }

    /**
     * Create order (raw).
     *
     * @param Client $client
     * @param array $request
     * @return array
     */
    public static function createRaw(Client $client, array $request): array
    {
        self::validateCreateRequest($request);

        return $client->requestData('POST', '/v3/orders', $request);
    }

    private static function validateCreateRequest(array $request): void
    {
        if (empty($request)) {
            throw new \InvalidArgumentException('Order request body must not be empty.');
        }

        if (!isset($request['products']) || !is_array($request['products']) || count($request['products']) === 0) {
            throw new \InvalidArgumentException('Order request must contain non-empty "products" array.');
        }

        // lehká validace položek
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

        // allowPreOrder je boolean pokud je přítomný
        if (isset($request['allowPreOrder']) && !is_bool($request['allowPreOrder'])) {
            // toleruj "0/1" nebo "true/false" stringy, pokud chceš – já to zatím držím striktní
            throw new \InvalidArgumentException('Order request allowPreOrder must be boolean.');
        }
    }
}
