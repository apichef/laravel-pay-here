<?php

namespace ApiChef\PayHere\Support\Facades;

use ApiChef\PayHere\DTO\PaymentDetails;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PaymentDetails getPaymentDetails(string $orderId)
 * @method static Collection getAllSubscriptions()
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
