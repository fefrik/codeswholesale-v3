<?php

namespace CodesWholesaleApi\Resource;

final class SecurityResultItem extends Resource
{
    public function isDomainBlacklisted(): ?bool
    {
        return $this->bool('domainBlacklisted');
    }

    public function isIpBlacklisted(): ?bool
    {
        return $this->bool('ipBlacklisted');
    }

    public function isIpTor(): ?bool
    {
        return $this->bool('ipTor');
    }

    public function getRiskScore(): ?int
    {
        return $this->int('riskScore');
    }

    public function isSubDomain(): ?bool
    {
        return $this->bool('subDomain');
    }
}
