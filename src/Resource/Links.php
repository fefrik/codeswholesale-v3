<?php

namespace CodesWholesaleApi\Resource;

final class Links implements \IteratorAggregate, \Countable
{
    /** @var array<int, LinkItem> */
    private array $items = [];

    /**
     * @param array<int, \stdClass> $rows
     */
    public function __construct(array $rows)
    {
        foreach ($rows as $row) {
            if ($row instanceof \stdClass) {
                $this->items[] = new LinkItem($row);
            }
        }
    }

    /**
     * @return \ArrayIterator<int, LinkItem>
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
