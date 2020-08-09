<?php

namespace ApiChef\PayHere\DTO;

class DeliveryDetails
{
    public $address;
    public $city;
    public $country;

    public function __construct(array $data)
    {
        $this->address = $data['address'];
        $this->city = $data['city'];
        $this->country = $data['country'];
    }
}
