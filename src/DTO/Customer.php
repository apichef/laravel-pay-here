<?php

namespace ApiChef\PayHere\DTO;

class Customer
{
    public $fistName;
    public $lastName;
    public $email;
    public $phone;
    public $deliveryDetails;
    public $data;

    public function __construct(array $data)
    {
        $this->fistName = $data['fist_name'];
        $this->lastName = $data['last_name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->deliveryDetails = $data['delivery_details'];
        $this->data = $data;
    }

    public function getDeliveryDetails()
    {
        return new DeliveryDetails($this->deliveryDetails);
    }
}
