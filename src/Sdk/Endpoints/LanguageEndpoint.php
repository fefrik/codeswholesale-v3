<?php

namespace CodesWholesaleApi\Sdk\Endpoints;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Resource\Language;
use CodesWholesaleApi\Resource\LanguageItem;

final class LanguageEndpoint
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all languages (typed objects).
     *
     * @return LanguageItem[]
     */
    public function getAll(): array
    {
        return Language::getAll($this->client);
    }

    /**
     * Fetch all languages as raw array.
     *
     * @return array
     */
    public function getAllRaw(): array
    {
        return Language::getAllRaw($this->client);
    }
}
