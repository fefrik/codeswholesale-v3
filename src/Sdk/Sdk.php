<?php

namespace CodesWholesaleApi;

use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Sdk\Endpoints\AccountEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\CodeEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\LanguageEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\OrderEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\PlatformEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\ProductEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\RegionEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\SecurityEndpoint;
use CodesWholesaleApi\Sdk\Endpoints\TerritoryEndpoint;

final class Sdk
{
    /** @var Client */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function product(): ProductEndpoint { return new ProductEndpoint($this->client); }
    public function orders(): OrderEndpoint { return new OrderEndpoint($this->client); }
    public function codes(): CodeEndpoint { return new CodeEndpoint($this->client); }
    public function account(): AccountEndpoint { return new AccountEndpoint($this->client); }
    public function security(): SecurityEndpoint { return new SecurityEndpoint($this->client); }

    public function platforms(): PlatformEndpoint { return new PlatformEndpoint($this->client); }
    public function regions(): RegionEndpoint { return new RegionEndpoint($this->client); }
    public function languages(): LanguageEndpoint { return new LanguageEndpoint($this->client); }
    public function territory(): TerritoryEndpoint { return new TerritoryEndpoint($this->client); }
}
