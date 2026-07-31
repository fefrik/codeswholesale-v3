<?php

namespace CodesWholesaleApi\Resource;

use CodesWholesaleApi\Enum\OrderStatus;

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

    public function getStatusType(): ?OrderStatus
    {
        $status = $this->getStatus();
        return $status === null ? null : OrderStatus::tryFrom(strtoupper($status));
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->dateTime('createdOn');
    }

    public function getTotalPrice(): ?float
    {
        return $this->float('totalPrice');
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
     * @return array<int, OrderProductItem>
     */
    public function getProducts(): array
    {
        return iterator_to_array($this->iterateProducts(), false);
    }

    /** @return \Generator<int, OrderProductItem, void, void> */
    public function iterateProducts(): \Generator
    {
        foreach ($this->iterateObjects('products') as $item) yield new OrderProductItem($item);
    }
}
