<?php

namespace CodesWholesaleApi\Resource;

class Links implements \IteratorAggregate, \Countable
{
    /** @var LinkItem[] */
    private $items = [];

    public function __construct(array $rows)
    {
        foreach ($rows as $row) {
            if (is_array($row)) {
                $this->items[] = new LinkItem($row);
            }
        }
    }

    /**
     * @return \ArrayIterator|LinkItem[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function first(): ?LinkItem
    {
        return $this->items[0] ?? null;
    }

    /**
     * Find link by rel.
     */
    public function findByRel(string $rel): ?LinkItem
    {
        foreach ($this->items as $item) {
            if ($item->getRel() === $rel) {
                return $item;
            }
        }
        return null;
    }

    public function toArray(): array
    {
        return array_map(function (LinkItem $l) {
            return $l->toArray();
        }, $this->items);
    }
}
