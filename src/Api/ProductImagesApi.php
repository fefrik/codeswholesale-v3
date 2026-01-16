<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\ProductImageItem;

final class ProductImagesApi
{
    private const ENDPOINT = '/v3/productImages/%s';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get product image by image id.
     *
     * @param string $id Image id (path param)
     * @return ProductImageItem|null
     */
    public function getById(string $id): ?ProductImageItem
    {
        $path = sprintf(self::ENDPOINT, rawurlencode($id));

        $data = $this->client->requestData('GET', $path);

        // kontrakt: 200 + {} považujeme za chybu
        if (empty(get_object_vars($data))) {
            return null;
        }

        return new ProductImageItem($data);
    }
}
