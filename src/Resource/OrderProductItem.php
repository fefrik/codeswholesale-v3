<?php

namespace CodesWholesaleApi\Resource;

final class OrderProductItem extends Resource
{
    public function __construct(\stdClass $data)
    {
        parent::__construct($data);
    }

    public function getProductId(): ?string
    {
        return $this->str('productId');
    }

    public function getName(): ?string
    {
        return $this->str('name');
    }

    public function getUnitPrice(): ?float
    {
        return $this->float('unitPrice');
    }

    /** @return array<int, LinkItem> */
    public function getLinks(): array
    {
        return iterator_to_array($this->iterateLinks(), false);
    }

    /** @return \Generator<int, LinkItem, void, void> */
    public function iterateLinks(): \Generator
    {
        foreach ($this->iterateObjects('links') as $item) yield new LinkItem($item);
    }

    /**
     * @return array<int, CodeItem>
     */
    public function getCodes(): array
    {
        return iterator_to_array($this->iterateCodes(), false);
    }

    /** @return \Generator<int, CodeItem, void, void> */
    public function iterateCodes(): \Generator
    {
        foreach ($this->iterateObjects('codes') as $item) yield new CodeItem($item);
    }
}
