<?php

namespace CodesWholesaleApi\Resource;

final class LinkItem extends Resource
{
    public function getRel(): ?string { return $this->str('rel'); }
    public function getHref(): ?string { return $this->str('href'); }
    public function getType(): ?string { return $this->str('type'); }
    public function getTitle(): ?string { return $this->str('title'); }
    public function getHreflang(): ?string { return $this->str('hreflang'); }

    public function isTemplated(): ?bool
    {
        return $this->bool('templated');
    }
}
