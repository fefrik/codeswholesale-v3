<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\ProductDescriptionItem;

final class ProductDescriptionApi
{
    private const PRODUCT_DESCRIPTION_ENDPOINT = '/v3/products/%s/description';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get product description by Product Identifier.
     *
     * @param string $productId
     * @param string|null $locale Preferred locale, e.g. "en", "cs-CZ"
     *
     * @return ProductDescriptionItem|null
     */
    public function getByProductId(string $productId, ?string $locale = null): ?ProductDescriptionItem
    {
        $query = [];
        if ($locale !== null && $locale !== '') {
            $query['locale'] = $locale;
        }

        $path = sprintf(
            self::PRODUCT_DESCRIPTION_ENDPOINT,
            rawurlencode($productId)
        );

        $data = $this->client->requestData(
            'GET',
            $path,
            null,
            $query
        );

        if (empty(get_object_vars($data))) {
            return null;
        }

        return new ProductDescriptionItem($data);
    }
}
