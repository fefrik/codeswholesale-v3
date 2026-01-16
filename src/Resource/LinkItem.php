<?php

namespace CodesWholesaleApi\Resource;

final class LinkItem extends Resource
{
    public function getDeprecation(): ?string { return $this->str('deprecation'); }
    public function getHref(): ?string        { return $this->str('href'); }
    public function getHreflang(): ?string    { return $this->str('hreflang'); }
    public function getMedia(): ?string       { return $this->str('media'); }
    public function getRel(): ?string         { return $this->str('rel'); }
    public function getTitle(): ?string       { return $this->str('title'); }
    public function getType(): ?string        { return $this->str('type'); }

    public function isTemplated(): bool
    {
        return (bool) $this->bool('templated');
    }
}

