<?php

declare(strict_types=1);

namespace ApiChef\PayHere\Tests;

use Apichef\PayHere\Exceptions\UnsupportedCurrencyException;
use ApiChef\PayHere\PayHere;
use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentTest extends TestCase
{
    public function test_payable()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();

        $this->assertInstanceOf(Product::class, $payment->payable);
    }

    public function test_payer()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();

        $this->assertInstanceOf(User::class, $payment->payer);
    }

    public function test_make()
    {
        $tom = factory(User::class)->create();
        $theBook = factory(Product::class)->create();
        $price = 20;
        $currency = PayHere::CURRENCY_USD;

        $payment = Payment::make($theBook, $tom, $price, $currency);

        $this->assertEquals($price, $payment->amount);
        $this->assertEquals($currency, $payment->currency);
        $this->assertEquals(0, $payment->status);
        $this->assertNull($payment->validated);
        $this->assertEquals($tom->id, $payment->payer->id);
        $this->assertEquals($theBook->id, $payment->payable->id);
    }

    public function test_make_currency_defaults_to_LKR()
    {
        $tom = factory(User::class)->create();
        $theBook = factory(Product::class)->create();
        $price = 20;

        $payment = Payment::make($theBook, $tom, $price);

        $this->assertEquals(PayHere::CURRENCY_LKR, $payment->currency);
    }

    public function test_make_throws_an_exception_when_the_given_currency_is_not_supported()
    {
        $tom = factory(User::class)->create();
        $theBook = factory(Product::class)->create();
        $price = 20;

        $this->expectException(UnsupportedCurrencyException::class);

        Payment::make($theBook, $tom, $price, 'SOS');
    }

    /**
     * @dataProvider paymentStatusAndSuccess
     */
    public function test_isPaid($status, $paid)
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create([
            'status' => $status,
        ]);

        $this->assertEquals($paid, $payment->isPaid());
    }

    public function test_scopePaidBy()
    {
        // payments of other users
        factory(Payment::class, 3)->create();

        $tom = factory(User::class)->create();

        // payments of other $tom
        factory(Payment::class, 2)
            ->create(['payer_id' => $tom->id]);

        $paymentsOfTom = Payment::paidBy($tom)->get();
        $this->assertCount(2, $paymentsOfTom);

        $this->assertTrue(true);
    }

    public function test_scopePaidFor()
    {
        // payments of other products
        factory(Payment::class, 3)->create();

        $theBook = factory(Product::class)->create();

        // payments of $theBook
        factory(Payment::class, 2)
            ->create(['payable_id' => $theBook->id]);

        $paymentsOfTom = Payment::paidFor($theBook)->get();
        $this->assertCount(2, $paymentsOfTom);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider paymentStatusAndSuccess
     */
    public function test_scopeSuccess($status, $success)
    {
        factory(Payment::class, 3)->create([
            'status' => $status,
        ]);

        $this->assertEquals($success, Payment::success()->get()->isNotEmpty());
    }

    public function paymentStatusAndSuccess(): array
    {
        return [
            [2, true],
            [1, true],
            [0, false],
            [-1, false],
            [-2, false],
            [-3, false],
        ];
    }

    public function test_refreshStatus()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->state('pending')->create();
        $this->assertEquals(0, $payment->status);

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            "sandbox.payhere.lk/merchant/v1/payment/search?order_id={$payment->getRouteKey()}" => Http::response([
                'status' => 1,
                'msg' => 'Payments with order_id:LP0000_2020-02-02',
                'data' => [],
            ]),
        ]);

        $payment->refreshStatus();
        $payment->fresh();

        $this->assertEquals(1, $payment->status);
    }

    public function test_refreshStatus_does_not_call_api_unless_the_status_is_0()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();
        $this->assertEquals(2, $payment->status);

        $payment->refreshStatus();
        $payment->fresh();

        Http::assertNotSent(function (Request $request) use ($payment) {
            return $request->url() === "sandbox.payhere.lk/merchant/v1/payment/search?order_id={$payment->getRouteKey()}";
        });
    }
}
