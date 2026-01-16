<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Http\HttpResponse;

final class ApiResponse
{
    /** @var HttpResponse */
    private HttpResponse $http;

    /** @var \stdClass */
    private \stdClass $data;

    public function __construct(HttpResponse $http, \stdClass $data)
    {
        $this->http = $http;
        $this->data = $data;
    }

    /**
     * Decoded JSON response body (top-level object).
     */
    public function getData(): \stdClass
    {
        return $this->data;
    }

    /**
     * Raw HTTP response wrapper.
     */
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
