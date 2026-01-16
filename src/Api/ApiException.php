<?php

namespace CodesWholesaleApi\Api;

use CodesWholesaleApi\Http\HttpResponse;

final class ApiException extends \RuntimeException
{
    /** @var HttpResponse */
    private HttpResponse $response;

    public function __construct(HttpResponse $response, string $message)
    {
        parent::__construct($message);
        $this->response = $response;
    }

    public function getResponse(): HttpResponse
    {
        return $this->response;
    }
}
