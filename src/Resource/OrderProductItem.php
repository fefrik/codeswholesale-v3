<?php

namespace CodesWholesaleApi\Resource;

final class OrderProductItem extends Resource
{
    private Links $links;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $rows = (isset($data->links) && is_array($data->links)) ? $data->links : [];
        $this->links = new Links($rows);
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

    public function getLinks(): Links
    {
        return $this->links;
    }

    /**
     * @return array<int, CodeItem>
     */
    public function getCodes(): array
    {
        $rows = (isset($this->data->codes) && is_array($this->data->codes)) ? $this->data->codes : [];

        $items = [];
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $items[] = new CodeItem($row);
            }
        }

        return $items;
    }
}
