<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\Exceptions\AuthorizationException;
use Illuminate\Support\Facades\Http;

class PayHereTest extends TestCase
{
    public function test_checkoutUrl()
    {
        $this->assertEquals(resolve('pay-here')->checkoutUrl(), 'https://sandbox.payhere.lk/pay/checkout');
    }

    public function test_it_throws_exception_when_pay_here_authentication_failed()
    {
        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([], 401),
        ]);

        $this->expectException(AuthorizationException::class);

        resolve('pay-here')->getPaymentDetails('order_007');
    }
}
