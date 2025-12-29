<?php

namespace CodesWholesaleApi\Resource;

class OrderProductItem
{
    /** @var array */
    private $data;

    private $links;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->links = new Links($data['links'] ?? []);
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

    public function getLinks(): Links
    {
        return $this->links;
    }

    /**
     * @return CodeItem[]
     */
    public function getCodes(): array
    {
        $rows = isset($this->data['codes']) && is_array($this->data['codes'])
            ? $this->data['codes']
            : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new CodeItem($row);
            }
        }
        return $items;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
