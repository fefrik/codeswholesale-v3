<?php

namespace CodesWholesaleApi\Resource;

class OrderProductItem
{
    /** @var array */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getProductId(): ?string
    {
        return $this->data['productId'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getUnitPrice(): ?float
    {
        return isset($this->data['unitPrice']) ? (float) $this->data['unitPrice'] : null;
    }

    public function getLinks(): array
    {
        return isset($this->data['links']) && is_array($this->data['links'])
            ? $this->data['links']
            : [];
    }

    /**
     * @return OrderCodeItem[]
     */
    public function getCodes(): array
    {
        $rows = isset($this->data['codes']) && is_array($this->data['codes'])
            ? $this->data['codes']
            : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new OrderCodeItem($row);
            }
        }
        return $items;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
