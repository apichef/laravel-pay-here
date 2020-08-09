<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\OrderDetails;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Http;

class PayHereTest extends TestCase
{
    public function test_checkoutUrl()
    {
        $this->assertEquals(resolve('pay-here')->checkoutUrl(), 'https://sandbox.payhere.lk/pay/checkout');
    }

    public function test_getOrderDetails()
    {
        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/payment/search?order_id=order_007' => Http::response([
                'status' => 1,
                'msg' => 'Payments with order_id:LP0000_2020-02-02',
                'data' => [
                    'foo' => 'bar',
                ],
            ]),
        ]);

        /** @var OrderDetails $orderDetails */
        $orderDetails = resolve('pay-here')->getOrderDetails('order_007');
        $this->assertInstanceOf(OrderDetails::class, $orderDetails);
        $this->assertEquals(1, $orderDetails->status);
        $this->assertEquals('Payments with order_id:LP0000_2020-02-02', $orderDetails->message);
        $this->assertEquals([
            'foo' => 'bar',
        ], $orderDetails->data);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Basic '.base64_encode('app_id:app_secret')) &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/oauth/token' &&
                $request['grant_type'] == 'client_credentials';
        });

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer pay-here-token') &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/payment/search?order_id=order_007';
        });
    }

    public function test_it_throws_exception_when_pay_here_authentication_failed()
    {
        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([], 401)
        ]);

        $this->expectException(AuthenticationException::class);

        resolve('pay-here')->getOrderDetails('order_007');
    }
}
