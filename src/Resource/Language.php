<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Api\Client;

class Language
{
    /**
     * Fetch all languages.
     *
     * @param Client $client
     * @return LanguageItem[]
     */
    public static function getAll(Client $client): array
    {
        $data = $client->requestData('GET', '/v3/languages');

        $rows = isset($data['languages']) && is_array($data['languages'])
            ? $data['languages']
            : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new LanguageItem($row);
            }
        }

        return $items;
    }

    /**
     * Fetch all languages as raw array.
     *
     * @param Client $client
     * @return array
     */
    public static function getAllRaw(Client $client): array
    {
        return $client->requestData('GET', '/v3/languages');
    }
}
