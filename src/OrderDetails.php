<?php

namespace Apichef\PayHere;

class OrderDetails
{
    public int $status;

    public string $message;

    public array $data;

    public function __construct(int $status, string $message, array $data)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}
