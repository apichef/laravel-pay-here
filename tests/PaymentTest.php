<?php

declare(strict_types=1);

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\DTO\Customer;
use ApiChef\PayHere\DTO\DeliveryDetails;
use ApiChef\PayHere\DTO\Item;
use ApiChef\PayHere\DTO\PaymentDetails;
use ApiChef\PayHere\DTO\PaymentMethod;
use ApiChef\PayHere\DTO\PriceDetails;
use Apichef\PayHere\Exceptions\UnsupportedCurrencyException;
use ApiChef\PayHere\PayHere;
use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
            [0, false],
            [-1, false],
            [-2, false],
            [-3, false],
        ];
    }

    public function test_findByOrderId()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();

        $this->assertEquals($payment->id, Payment::findByOrderId($payment->getRouteKey())->id);
    }

    /**
     * @dataProvider paymentAmounts
     */
    public function test_getAmountAttribute($value, $rounded)
    {
        $payment = factory(Payment::class)->create([
            'amount' => $value,
        ]);

        $this->assertEquals($rounded, $payment->amount);
    }

    public function paymentAmounts(): array
    {
        return [
            [1, 1.00],
            [100, 100.00],
            [100.55, 100.55],
            [100.333, 100.33],
            [100.5555, 100.56],
            [1000.5555, 1000.56],
        ];
    }

    public function test_hash_accessor()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();

        $this->assertEquals($payment->hash, '8860B3F13E489ACFBBD3C2B852DB409F');
    }

    public function test_getPaymentDetails()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();
        $orderId = $payment->getRouteKey();

        $responseData = [
            'status' => 1,
            'msg' => 'Payments with order_id:LP8006126139_2019-12-06',
            'data' => [
                [
                    'payment_id' => 320025071278,
                    'order_id' => 'LP8006126139',
                    'date' => '2020-01-16 16:21:02',
                    'description' => 'Policy No. LP8006126139 - Outstanding Payment',
                    'status' => 'RECEIVED',
                    'currency' => 'LKR',
                    'amount' => 50,
                    'customer' => [
                        'fist_name' => 'Saman',
                        'last_name' => 'Perera',
                        'email' => 'samanperera@gmail.com',
                        'phone' => '+94771234567',
                        'delivery_details' => [
                            'address' => 'N0.1, Galle Road',
                            'city' => 'Colombo',
                            'country' => 'Sri Lanka',
                        ],
                    ],
                    'amount_detail' => [
                        'currency' => 'LKR',
                        'gross' => 500,
                        'fee' => 14.5,
                        'net' => 485.5,
                        'exchange_rate' => 1,
                        'exchange_from' => 'LKR',
                        'exchange_to' => 'LKR',
                    ],
                    'payment_method' => [
                        'method' => 'VISA',
                        'card_customer_name' => 'S Perera',
                        'card_no' => '************1234',
                    ],
                    'items' => [
                        [
                            "name" => "Book reading Subscription",
                            "quantity" => 1,
                            "currency" => "LKR",
                            "unit_price" => 100,
                            "total_price" => 100
                        ]
                    ],
                ],
            ],
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            "sandbox.payhere.lk/merchant/v1/payment/search?order_id={$orderId}" => Http::response($responseData),
        ]);

        $paymentDetails = $payment->getPaymentDetails();

        $this->assertInstanceOf(PaymentDetails::class, $paymentDetails);
        $this->assertEquals(1, $paymentDetails->statusCode);
        $this->assertEquals($responseData['data'][0], $paymentDetails->data);

        $this->assertEquals($responseData['data'][0]['payment_id'], $paymentDetails->paymentId);
        $this->assertEquals($responseData['data'][0]['order_id'], $paymentDetails->orderId);
        $this->assertEquals($responseData['data'][0]['date'], $paymentDetails->date->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(Carbon::class, $paymentDetails->date);
        $this->assertEquals($responseData['data'][0]['description'], $paymentDetails->description);
        $this->assertEquals($responseData['data'][0]['currency'], $paymentDetails->currency);
        $this->assertEquals($responseData['data'][0]['amount'], $paymentDetails->amount);

        $customerObject = $paymentDetails->getCustomer();
        $this->assertInstanceOf(Customer::class, $customerObject);
        $customer = $responseData['data'][0]['customer'];
        $this->assertEquals($customer, $paymentDetails->customer);

        $this->assertEquals($customer['fist_name'], $customerObject->fistName);
        $this->assertEquals($customer['last_name'], $customerObject->lastName);
        $this->assertEquals($customer['email'], $customerObject->email);
        $this->assertEquals($customer['phone'], $customerObject->phone);

        $deliveryDetailsObject = $customerObject->getDeliveryDetails();
        $this->assertInstanceOf(DeliveryDetails::class, $deliveryDetailsObject);
        $deliveryDetails = $customer['delivery_details'];
        $this->assertEquals($deliveryDetails, $customerObject->deliveryDetails);

        $this->assertEquals($deliveryDetails['address'], $deliveryDetailsObject->address);
        $this->assertEquals($deliveryDetails['city'], $deliveryDetailsObject->city);
        $this->assertEquals($deliveryDetails['country'], $deliveryDetailsObject->country);

        $priceDetailsObject = $paymentDetails->getPriceDetails();
        $this->assertInstanceOf(PriceDetails::class, $priceDetailsObject);
        $amountDetail = $responseData['data'][0]['amount_detail'];
        $this->assertEquals($amountDetail, $paymentDetails->amountDetail);

        $this->assertEquals($amountDetail['currency'], $priceDetailsObject->currency);
        $this->assertEquals($amountDetail['gross'], $priceDetailsObject->gross);
        $this->assertEquals($amountDetail['fee'], $priceDetailsObject->fee);
        $this->assertEquals($amountDetail['net'], $priceDetailsObject->net);
        $this->assertEquals($amountDetail['exchange_rate'], $priceDetailsObject->exchangeRate);
        $this->assertEquals($amountDetail['exchange_from'], $priceDetailsObject->exchangeFrom);
        $this->assertEquals($amountDetail['exchange_to'], $priceDetailsObject->exchangeTo);

        $paymentMethodObject = $paymentDetails->getPaymentMethod();
        $this->assertInstanceOf(PaymentMethod::class, $paymentMethodObject);
        $paymentMethod = $responseData['data'][0]['payment_method'];
        $this->assertEquals($paymentMethod, $paymentDetails->paymentMethod);

        $this->assertEquals($paymentMethod['method'], $paymentMethodObject->method);
        $this->assertEquals($paymentMethod['card_customer_name'], $paymentMethodObject->nameOnCard);
        $this->assertEquals($paymentMethod['card_no'], $paymentMethodObject->cardNumber);

        $items = $paymentDetails->getItems();

        $this->assertInstanceOf(Collection::class, $items);
        $this->assertInstanceOf(Item::class, $items->first());
        $item = $responseData['data'][0]['items'][0];

        /** @var Item $itemObject */
        $itemObject = $items->first();
        $this->assertEquals($item['name'], $itemObject->name);
        $this->assertEquals($item['quantity'], $itemObject->quantity);
        $this->assertEquals($item['currency'], $itemObject->currency);
        $this->assertEquals($item['unit_price'], $itemObject->unitPrice);
        $this->assertEquals($item['total_price'], $itemObject->totalPrice);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Basic '.base64_encode('test_app_id:test_app_secret')) &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/oauth/token' &&
                $request['grant_type'] == 'client_credentials';
        });

        Http::assertSent(function ($request) use ($orderId) {
            return $request->hasHeader('Authorization', 'Bearer pay-here-token') &&
                $request->url() == "https://sandbox.payhere.lk/merchant/v1/payment/search?order_id={$orderId}";
        });
    }
}
