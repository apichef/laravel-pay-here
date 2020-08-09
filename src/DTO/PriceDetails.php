<?php

namespace ApiChef\PayHere\DTO;

class PriceDetails
{
    public $currency;
    public $gross;
    public $fee;
    public $net;
    public $exchangeRate;
    public $exchangeFrom;
    public $exchangeTo;

    public function __construct(array $data)
    {
        $this->currency = $data['currency'];
        $this->gross = $data['gross'];
        $this->fee = $data['fee'];
        $this->net = $data['net'];
        $this->exchangeRate = $data['exchange_rate'];
        $this->exchangeFrom = $data['exchange_from'];
        $this->exchangeTo = $data['exchange_to'];
    }
}
