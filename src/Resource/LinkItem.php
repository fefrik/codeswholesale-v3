<?php

namespace CodesWholesaleApi\Resource;

class LinkItem
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getRel(): ?string { return $this->data['rel'] ?? null; }
    public function getHref(): ?string { return $this->data['href'] ?? null; }
    public function getType(): ?string { return $this->data['type'] ?? null; }
    public function getTitle(): ?string { return $this->data['title'] ?? null; }
    public function getHreflang(): ?string { return $this->data['hreflang'] ?? null; }
    public function isTemplated(): ?bool
    {
        return isset($this->data['templated']) ? (bool)$this->data['templated'] : null;
    }

    public function toArray(): array { return $this->data; }
}
