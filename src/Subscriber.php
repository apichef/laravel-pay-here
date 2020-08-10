<?php

namespace ApiChef\PayHere;

use Illuminate\Database\Eloquent\Model;

trait Subscriber
{
    public function hasActiveSubscription(Model $subscribable)
    {
        return Subscription::query()
            ->paidBy($this)
            ->subscribedTo($subscribable)
            ->active()
            ->exists();
    }
}
