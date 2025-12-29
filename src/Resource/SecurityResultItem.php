<?php

namespace CodesWholesaleApi\Resource;

class SecurityResultItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isDomainBlacklisted(): ?bool
    {
        return isset($this->data['domainBlacklisted']) ? (bool) $this->data['domainBlacklisted'] : null;
    }

    public function isIpBlacklisted(): ?bool
    {
        return isset($this->data['ipBlacklisted']) ? (bool) $this->data['ipBlacklisted'] : null;
    }

    public function isIpTor(): ?bool
    {
        return isset($this->data['ipTor']) ? (bool) $this->data['ipTor'] : null;
    }

    public function getRiskScore(): ?int
    {
        return isset($this->data['riskScore']) ? (int) $this->data['riskScore'] : null;
    }

    public function isSubDomain(): ?bool
    {
        return isset($this->data['subDomain']) ? (bool) $this->data['subDomain'] : null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
