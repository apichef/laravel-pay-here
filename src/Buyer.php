<?php

namespace ApiChef\PayHere;

use Illuminate\Database\Eloquent\Model;

trait Buyer
{
    public function hasBought(Model $payable)
    {
        return Payment::query()
            ->paidBy($this)
            ->paidFor($payable)
            ->success()
            ->exists();
    }
}
