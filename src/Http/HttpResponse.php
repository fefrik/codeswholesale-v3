<?php

namespace CodesWholesaleApi\Http;

final class HttpResponse
{
    /** @var int */
    private int $status;

    /** @var string */
    private string $rawBody;

    /** @var \stdClass|null */
    private ?\stdClass $jsonBody;

    /** @var array */
    private array $headers;

    public function __construct(
        int $status,
        string $rawBody,
        ?\stdClass $jsonBody = null,
        array $headers = []
    ) {
        $this->status = $status;
        $this->rawBody = $rawBody;
        $this->jsonBody = $jsonBody;
        $this->headers = $headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    /**
     * Decoded JSON body (top-level object).
     * Returns null if body was empty or invalid JSON.
     */
    public function getJsonBody(): ?\stdClass
    {
        return $this->jsonBody;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function isSuccess(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function isUnauthorized(): bool
    {
        return $this->status === 401;
    }

    /**
     * Strict helper – throws if response body is not a JSON object.
     */
    public function jsonObject(): \stdClass
    {
        if (!$this->jsonBody instanceof \stdClass) {
            throw new \RuntimeException(
                "Response body is not a JSON object. HTTP {$this->status}."
            );
        }

        return $this->jsonBody;
    }
}
