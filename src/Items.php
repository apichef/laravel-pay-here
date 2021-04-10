<?php declare(strict_types=1);

namespace ApiChef\PayHere;

use ArrayIterator;
use IteratorAggregate;

class Items implements IteratorAggregate
{
    /** @var Item[] */
    private array $items;

    public function __construct(Item ...$items)
    {
        $this->items = $items;
    }

    public function app(Item $item): void
    {
        $this->items[] = $item;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
