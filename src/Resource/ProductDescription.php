<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class ProductDescription
{
    /**
     * Get product description by Product Identifier.
     *
     * @param Client $client
     * @param string $productId
     * @param string|null $locale Preferred locale, e.g. "en", "cs-CZ", etc. If null, API defaults to English.
     *
     * @return ProductDescriptionItem|null
     */
    public static function getByProductId(Client $client, string $productId, ?string $locale = null): ?ProductDescriptionItem
    {
        $query = [];
        if ($locale !== null && $locale !== '') {
            $query['locale'] = $locale;
        }

        $data = $client->requestData(
            'GET',
            '/v3/products/' . rawurlencode($productId) . '/description',
            null,
            $query
        );

        return !empty($data) ? new ProductDescriptionItem($data) : null;
    }

    /**
     * Raw variant (if you don't want DTO).
     */
    public static function getByProductIdRaw(Client $client, string $productId, ?string $locale = null): array
    {
        $query = [];
        if ($locale !== null && $locale !== '') {
            $query['locale'] = $locale;
        }

        return $client->requestData(
            'GET',
            '/v3/products/' . rawurlencode($productId) . '/description',
            null,
            $query
        );
    }
}
