<?php

namespace CodesWholesaleApi\Resource;

class CodeItem
{
    /** @var array */
    private $data;
    private $links;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->links = new Links($data['links'] ?? []);
    }

    public function getCodeId(): ?string { return $this->data['codeId'] ?? null; }
    public function getCodeType(): ?string { return $this->data['codeType'] ?? null; }
    public function getCode(): ?string { return $this->data['code'] ?? null; }
    public function getFilename(): ?string { return $this->data['filename'] ?? null; }

    public function getLinks(): Links
    {
        return $this->links;
    }

    public function toArray(): array { return $this->data; }
}
