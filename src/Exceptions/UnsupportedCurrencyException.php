<?php

namespace ApiChef\PayHere\Exceptions;

use ApiChef\PayHere\Support\Facades\PayHere;
use InvalidArgumentException;

class UnsupportedCurrencyException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Unsupported currency. Allowed only: '.implode(',', PayHere::allowedCurrencies()));
    }
}
