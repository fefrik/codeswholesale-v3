<?php

namespace CodesWholesaleApi\Resource;

final class BadgeItem extends Resource
{
    public function getId(): ?int { return $this->int('id'); }
    public function getName(): ?string { return $this->str('name'); }
    public function getSlug(): ?string { return $this->str('slug'); }
}
