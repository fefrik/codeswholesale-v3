<?php

namespace CodesWholesaleApi\Sdk;

use CodesWholesaleApi\Api\AccountApi;
use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Api\CodesApi;
use CodesWholesaleApi\Api\LanguagesApi;
use CodesWholesaleApi\Api\OrdersApi;
use CodesWholesaleApi\Api\PlatformsApi;
use CodesWholesaleApi\Api\ProductDescriptionApi;
use CodesWholesaleApi\Api\ProductImagesApi;
use CodesWholesaleApi\Api\ProductsApi;
use CodesWholesaleApi\Api\RegionsApi;
use CodesWholesaleApi\Api\SecurityApi;
use CodesWholesaleApi\Api\TerritoriesApi;

final class Sdk
{
    private Client $client;

    // volitelně cachujeme instance, ať je nevytváříš pořád dokola
    private ?ProductsApi $products = null;
    private ?OrdersApi $orders = null;
    private ?CodesApi $codes = null;
    private ?AccountApi $account = null;
    private ?SecurityApi $security = null;

    private ?PlatformsApi $platforms = null;
    private ?RegionsApi $regions = null;
    private ?LanguagesApi $languages = null;
    private ?TerritoriesApi $territories = null;

    private ?ProductDescriptionApi $productDescriptions = null;
    private ?ProductImagesApi $productImages = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function products(): ProductsApi
    {
        return $this->products ?: ($this->products = new ProductsApi($this->client));
    }

    public function orders(): OrdersApi
    {
        return $this->orders ?: ($this->orders = new OrdersApi($this->client));
    }

    public function codes(): CodesApi
    {
        return $this->codes ?: ($this->codes = new CodesApi($this->client));
    }

    public function account(): AccountApi
    {
        return $this->account ?: ($this->account = new AccountApi($this->client));
    }

    public function security(): SecurityApi
    {
        return $this->security ?: ($this->security = new SecurityApi($this->client));
    }

    public function platforms(): PlatformsApi
    {
        return $this->platforms ?: ($this->platforms = new PlatformsApi($this->client));
    }

    public function regions(): RegionsApi
    {
        return $this->regions ?: ($this->regions = new RegionsApi($this->client));
    }

    public function languages(): LanguagesApi
    {
        return $this->languages ?: ($this->languages = new LanguagesApi($this->client));
    }

    public function territories(): TerritoriesApi
    {
        return $this->territories ?: ($this->territories = new TerritoriesApi($this->client));
    }

    public function productDescriptions(): ProductDescriptionApi
    {
        return $this->productDescriptions ?: ($this->productDescriptions = new ProductDescriptionApi($this->client));
    }

    public function productImages(): ProductImagesApi
    {
        return $this->productImages ?: ($this->productImages = new ProductImagesApi($this->client));
    }
}
