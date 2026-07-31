<?php

namespace CodesWholesaleApi\Resource;

final class FactSheetItem extends Resource
{
    public function getDescription(): ?string { return $this->str('description'); }
    public function getTerritory(): ?string { return $this->str('territory'); }
}
