<?php

namespace CodesWholesaleApi\Resource;

final class OrderItem extends Resource
{
    private Links $links;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $rows = (isset($data->links) && is_array($data->links)) ? $data->links : [];
        $this->links = new Links($rows);
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

    public function getLinks(): Links
    {
        return $this->links;
    }
}
