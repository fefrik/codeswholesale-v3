<?php

namespace CodesWholesaleApi\Resource;

final class ReleaseItem extends Resource
{
    public function getReleaseDateRaw(): ?string { return $this->str('releaseDate'); }
    public function getReleaseDate(): ?\DateTimeImmutable { return $this->dateTime('releaseDate'); }
    public function getReleaseStatus(): ?string { return $this->str('releaseStatus'); }
    public function getTerritory(): ?string { return $this->str('territory'); }
}
