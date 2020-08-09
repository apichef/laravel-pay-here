<?php

namespace ApiChef\PayHere\DTO;

class PaymentDetails extends PayableDetails
{
    public $paymentId;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->paymentId = $this->data['payment_id'];
    }
}
