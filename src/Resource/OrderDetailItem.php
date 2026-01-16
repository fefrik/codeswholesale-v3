<?php

namespace CodesWholesaleApi\Resource;

final class OrderDetailItem extends Resource
{

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);
    }

    public function getOrderId(): ?string
    {
        return $this->str('orderId');
    }

    public function getClientOrderId(): ?string
    {
        return $this->str('clientOrderId');
    }

    public function getIdentifier(): ?string
    {
        return $this->str('identifier');
    }

    public function getStatus(): ?string
    {
        return $this->str('status');
    }

    public function getCreatedOn(): ?string
    {
        return $this->str('createdOn');
    }

    public function getTotalPrice(): ?float
    {
        return $this->float('totalPrice');
    }

    /** @return array<int, LinkItem> */
    public function getLinks(): array
    {
        return array_map(
            function (\stdClass $p) {
                return new LinkItem($p);
            },
            $this->list('links')
        );
    }

    /**
     * @return array<int, OrderProductItem>
     */
    public function getProducts(): array
    {
        return array_map(
            function (\stdClass $p) {
                return new OrderProductItem($p);
            },
            $this->list('products')
        );
    }
}
