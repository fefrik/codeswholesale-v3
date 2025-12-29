<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class ProductImage
{
    /**
     * Get product image by image id.
     *
     * @param Client $client
     * @param string $id Image id (path param)
     * @return ProductImageItem|null
     */
    public static function getById(Client $client, string $id): ?ProductImageItem
    {
        $data = $client->requestData('GET', '/v3/productImages/' . rawurlencode($id));

        return !empty($data) ? new ProductImageItem($data) : null;
    }

    /**
     * Get product image raw payload by image id.
     *
     * @param Client $client
     * @param string $id
     * @return array
     */
    public static function getByIdRaw(Client $client, string $id): array
    {
        return $client->requestData('GET', '/v3/productImages/' . rawurlencode($id));
    }
}
