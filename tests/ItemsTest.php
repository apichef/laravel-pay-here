<?php

declare(strict_types=1);

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\Item;
use ApiChef\PayHere\Items;
use IteratorAggregate;

class ItemsTest extends \PHPUnit\Framework\TestCase
{
    public function test_can_create_an_empty_items_instance()
    {
        $items = new Items();
        $this->assertInstanceOf(Items::class, $items);
    }

    public function test_can_create_an_iteratorable_items_instance()
    {
        $items = new Items(...[
            new Item('001', 'First book', 100, 1),
            new Item('002', 'Second book', 200, 2),
        ]);

        $this->assertInstanceOf(Items::class, $items);
        $this->assertInstanceOf(IteratorAggregate::class, $items);
        $this->assertCount(2, $items);
    }

    public function test_can_add_item()
    {
        $items = new Items(...[
            new Item('001', 'First book', 100, 1),
            new Item('002', 'Second book', 200, 2),
        ]);

        $items->app(new Item('003', 'Third book', 300, 3));

        $this->assertInstanceOf(Items::class, $items);
        $this->assertCount(3, $items);
    }
}
