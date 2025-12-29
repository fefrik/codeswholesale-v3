<?php

namespace CodesWholesaleApi\Resource;

class OrderCodeItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getCodeId(): ?string
    {
        return $this->data['codeId'] ?? null;
    }

    public function getCodeType(): ?string
    {
        return $this->data['codeType'] ?? null;
    }

    public function getCode(): ?string
    {
        return $this->data['code'] ?? null;
    }

    public function getFilename(): ?string
    {
        return $this->data['filename'] ?? null;
    }

    public function getLinks(): array
    {
        return isset($this->data['links']) && is_array($this->data['links'])
            ? $this->data['links']
            : [];
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
