<?php

namespace CodesWholesaleApi\Http;

final class HttpResponse
{
    /** @var int */
    private $status;

    /** @var string */
    private $rawBody;

    /** @var array|null */
    private $jsonBody;

    /** @var array */
    private $headers;

    public function __construct(
        int $status,
        string $rawBody,
        ?array $jsonBody = null,
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

    public function getJsonBody(): ?array
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

    public function json(): array
    {
        if ($this->jsonBody === null) {
            throw new \RuntimeException(
                "Response body is not valid JSON. HTTP {$this->status}."
            );
        }

        return $this->jsonBody;
    }
}
