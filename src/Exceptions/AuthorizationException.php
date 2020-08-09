<?php

namespace ApiChef\PayHere\Exceptions;

class AuthorizationException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unable to authenticate PayHere');
    }
}
