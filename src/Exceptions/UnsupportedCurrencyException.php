<?php

namespace ApiChef\PayHere\Exceptions;

use ApiChef\PayHere\Support\Facades\PayHere;

class UnsupportedCurrencyException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unsupported currency. Allowed only: '.implode(',', PayHere::allowedCurrencies()));
    }
}
