<?php

namespace ApiChef\PayHere\Tests\Http\Controllers;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\TestCase;

class PaymentRedirectControllerTest extends TestCase
{
    public function test_success()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();
        $orderId = $payment->getRouteKey();

        $this->get(route('pay-here.payment-success', ['order_id' => $orderId]))
            ->assertRedirect(route(config('pay-here.routes_name.payment_success'), $payment));
    }

    public function test_canceled()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();
        $orderId = $payment->getRouteKey();

        $this->get(route('pay-here.payment-canceled', ['order_id' => $orderId]))
            ->assertRedirect(route(config('pay-here.routes_name.payment_canceled'), $payment));
    }
}
