<?php

namespace ApiChef\PayHere\DTO;

class PaymentMethod
{
    public $method;
    public $nameOnCard;
    public $cardNumber;

    public function __construct(array $data)
    {
        $this->method = $data['method'];
        $this->nameOnCard = $data['card_customer_name'];
        $this->cardNumber = $data['card_no'];
    }
}
