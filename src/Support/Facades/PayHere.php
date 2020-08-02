<?php

namespace ApiChef\PayHere\Support\Facades;

use Apichef\PayHere\OrderDetails;
use Illuminate\Support\Facades\Facade;

/**
 * @method static OrderDetails getOrderDetails(string $orderId)
 * @method static string checkoutUrl()
 * @method static array allowedCurrencies()
 *
 * @see \ApiChef\PayHere\PayHere
 */
class PayHere extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pay-here';
    }
}
