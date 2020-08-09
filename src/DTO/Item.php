<?php

namespace ApiChef\PayHere\DTO;

class Item
{
    public $name;
    public $quantity;
    public $currency;
    public $unitPrice;
    public $totalPrice;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->quantity = $data['quantity'];
        $this->currency = $data['currency'];
        $this->unitPrice = $data['unit_price'];
        $this->totalPrice = $data['total_price'];
    }
}
