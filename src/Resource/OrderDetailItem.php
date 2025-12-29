<?php

namespace CodesWholesaleApi\Resource;

class OrderDetailItem
{
    /** @var array */
    private $data;
    private $links;


    public function __construct(array $data)
    {
        $this->data = $data;
        $this->links = new Links($data['links'] ?? []);
    }

    public function getOrderId(): ?string
    {
        return $this->data['orderId'] ?? null;
    }

    public function getClientOrderId(): ?string
    {
        return $this->data['clientOrderId'] ?? null;
    }

    public function getIdentifier(): ?string
    {
        return $this->data['identifier'] ?? null;
    }

    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    public function getCreatedOn(): ?string
    {
        return $this->data['createdOn'] ?? null;
    }

    public function getTotalPrice(): ?float
    {
        return isset($this->data['totalPrice']) ? (float) $this->data['totalPrice'] : null;
    }

    public function getLinks(): Links
    {
        return $this->links;
    }

    /**
     * @return OrderProductItem[]
     */
    public function getProducts(): array
    {
        $rows = isset($this->data['products']) && is_array($this->data['products'])
            ? $this->data['products']
            : [];

        $items = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $items[] = new OrderProductItem($row);
            }
        }
        return $items;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
