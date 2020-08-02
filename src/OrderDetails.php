<?php

namespace ApiChef\PayHere;

class OrderDetails
{
    public $status;

    public $message;

    public $data;

    public function __construct(int $status, string $message, array $data)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}
