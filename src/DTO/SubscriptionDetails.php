<?php

namespace ApiChef\PayHere\DTO;

class SubscriptionDetails extends PayableDetails
{
    public $subscriptionId;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->subscriptionId = $this->data['subscription_id'];
    }
}
