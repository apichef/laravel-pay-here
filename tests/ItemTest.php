<?php declare(strict_types=1);

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\Item;

class ItemTest extends \PHPUnit\Framework\TestCase
{
    public function test_can_init_item()
    {
        $id = 'i_001';
        $name = 'the item';
        $amount = 10.75;
        $quantity = 2;

        $item = new Item($id, $name, $amount, $quantity);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals($id, $item->getId());
        $this->assertEquals($name, $item->getName());
        $this->assertEquals($amount, $item->getAmount());
        $this->assertEquals($quantity, $item->getQuantity());
    }
}
