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
        $links = [];
        foreach ($this->list('links') as $linkData) {
            $links[] = new LinkItem($linkData);
        }
        return $links;
    }

    /**
     * @return array<int, CodeItem>
     */
    public function getCodes(): array
    {
        $items = [];
        foreach ($this->list('codes') as $row) {
            $items[] = new CodeItem($row);
        }
        return $items;
    }
}
