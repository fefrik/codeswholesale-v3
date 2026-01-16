<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Resource\LanguageItem;

final class LanguagesApi
{
    private const LANGUAGES_ENDPOINT = '/v3/languages';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all languages.
     *
     * @return array<int, LanguageItem>
     */
    public function getAll(): array
    {
        $data = $this->client->requestData('GET', self::LANGUAGES_ENDPOINT);

        $rows = (isset($data->languages) && is_array($data->languages)) ? $data->languages : [];

        $items = [];
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new LanguageItem($row);
            }
        }
        return $items;
    }
}
