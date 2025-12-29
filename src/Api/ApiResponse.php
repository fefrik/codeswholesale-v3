<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Http\HttpResponse;

final class ApiResponse
{
    /** @var HttpResponse */
    private $http;

    /** @var array */
    private $data;

    public function __construct(HttpResponse $http, array $data)
    {
        $this->http = $http;
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getHttp(): HttpResponse
    {
        return $this->http;
    }

    public function getStatus(): int
    {
        return $this->http->getStatus();
    }

    public function getRaw(): string
    {
        return $this->http->getRawBody();
    }
}
