<?php

declare(strict_types=1);

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\DTO\PaymentDetails;
use ApiChef\PayHere\Exceptions\InvalidTokenException;
use ApiChef\PayHere\Exceptions\NotAllowedToCancelException;
use ApiChef\PayHere\Exceptions\NotEligibleForRetryingException;
use ApiChef\PayHere\PayHere;
use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Illuminate\Support\Facades\Http;

class SubscriptionTest extends TestCase
{
    public function test_make()
    {
        $tom = factory(User::class)->create();
        $theBook = factory(Product::class)->create();
        $price = 20;
        $currency = PayHere::CURRENCY_USD;

        $subscription = Subscription::make($theBook, $tom, '1 Month', 'Forever', $price, $currency);

        $this->assertEquals($price, $subscription->amount);
        $this->assertEquals($currency, $subscription->currency);
        $this->assertEquals(0, $subscription->status);
        $this->assertNull($subscription->validated);
        $this->assertEquals($tom->id, $subscription->payer->id);
        $this->assertEquals($theBook->id, $subscription->subscribable->id);
    }

    public function test_findByOrderId()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();

        $this->assertEquals($subscription->id, Subscription::findByOrderId($subscription->getRouteKey())->id);
    }

    public function test_getSubscriptionPayments()
    {
        $responseData = [
            'status' => 1,
            'msg' => 'Found 2 payments',
            'data' => [
                [
                    'payment_id' => 320025023469,
                    'order_id' => 'Order0003',
                    'date' => '2018-10-04 20:24:52',
                    'description' => 'Book reading Subscription',
                    'status' => 'RECEIVED',
                    'currency' => 'LKR',
                    'amount' => 200,
                    'customer' => [
                        'fist_name' => 'Saman',
                        'last_name' => 'Kumara',
                        'email' => 'saman@gmail.com',
                        'phone' => '+94712345678',
                        'delivery_details' => [
                            'address' => '1, Galle Road',
                            'city' => 'Colombo',
                            'country' => '',
                        ],
                    ],
                    'amount_detail' => [
                        'currency' => 'LKR',
                        'gross' => 200,
                        'fee' => 36.6,
                        'net' => 163.4,
                        'exchange_rate' => 1,
                        'exchange_from' => 'LKR',
                        'exchange_to' => 'LKR',
                    ],
                    'payment_method' => [
                        'method' => 'VISA',
                        'card_customer_name' => 'Saman Kumara',
                        'card_no' => '************4564',
                    ],
                    'items' => [
                        [
                            'name' => 'Book reading Subscription',
                            'quantity' => 1,
                            'currency' => 'LKR',
                            'unit_price' => 100,
                            'total_price' => 100,
                        ],
                        [
                            'name' => 'Startup Fee',
                            'quantity' => 1,
                            'currency' => 'LKR',
                            'unit_price' => 100,
                            'total_price' => 100,
                        ],
                    ],
                ],
                [
                    'payment_id' => 320025023470,
                    'order_id' => 'Order0003',
                    'date' => '2018-10-04 20:25:52',
                    'description' => 'Book reading Subscription',
                    'status' => 'RECEIVED',
                    'currency' => 'LKR',
                    'amount' => 100,
                    'customer' => [
                        'fist_name' => 'Saman',
                        'last_name' => 'Kumara',
                        'email' => 'saman@gmail.com',
                        'phone' => '+94712345678',
                        'delivery_details' => [
                            'address' => '1, Galle Road',
                            'city' => 'Colombo',
                            'country' => '',
                        ],
                    ],
                    'amount_detail' => [
                        'currency' => 'LKR',
                        'gross' => 100,
                        'fee' => 34.3,
                        'net' => 65.7,
                        'exchange_rate' => 1,
                        'exchange_from' => 'LKR',
                        'exchange_to' => 'LKR',
                    ],
                    'payment_method' => [
                        'method' => 'VISA',
                        'card_customer_name' => 'Saman Kumara',
                        'card_no' => '************4564',
                    ],
                    'items' => [
                        [
                            'name' => 'Book reading Subscription',
                            'quantity' => 1,
                            'currency' => 'LKR',
                            'unit_price' => 100,
                            'total_price' => 100,
                        ],
                    ],
                ],
            ],
        ];

        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $paymentsUri = "sandbox.payhere.lk/merchant/v1/subscription/{$subscription->subscription_id}/payments";
        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            $paymentsUri => Http::response($responseData),
        ]);

        $payments = $subscription->getPayments();

        $this->assertInstanceOf(PaymentDetails::class, $payments->first());

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Basic '.base64_encode('test_app_id:test_app_secret')) &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/oauth/token' &&
                $request['grant_type'] == 'client_credentials';
        });

        Http::assertSent(function ($request) use ($paymentsUri) {
            return $request->hasHeader('Authorization', 'Bearer pay-here-token') &&
                $request->url() == 'https://'.$paymentsUri;
        });
    }

    public function test_getSubscriptionPayments_invalid_token()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            'error' => 'invalid_token',
            'error_description' => 'Invalid access token: e291493a-99a5-4177-9c8b-e8cd18ee9f85',
        ];

        $paymentsUri = "sandbox.payhere.lk/merchant/v1/subscription/{$subscription->subscription_id}/payments";
        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            $paymentsUri => Http::response($responseData, 401),
        ]);

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage($responseData['error_description']);

        $subscription->getPayments();
    }

    public function test_retry()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            "status" => 1,
            "msg" => "Recurring payment charged successfully",
            "data" => null
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription/retry' => Http::response($responseData),
        ]);

        $subscription->retry();

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Basic '.base64_encode('test_app_id:test_app_secret')) &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/oauth/token' &&
                $request['grant_type'] == 'client_credentials';
        });

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer pay-here-token') &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/subscription/retry' &&
                $request['subscription_id'] == 'a_unique_subscription_id';
        });
    }

    public function test_it_throws_an_exception_when_not_eligible_for_retrying()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            "status" => -1,
            "msg" => "Subscription is not eligible for retrying",
            "data" => null
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription/retry' => Http::response($responseData, 422),
        ]);

        $this->expectException(NotEligibleForRetryingException::class);
        $this->expectExceptionMessage($responseData['msg']);

        $subscription->retry();
    }

    public function test_it_throws_an_exception_when_retrying_weith_invalid_token()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            'error' => 'invalid_token',
            'error_description' => 'Invalid access token: e291493a-99a5-4177-9c8b-e8cd18ee9f85',
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription/retry' => Http::response($responseData, 401),
        ]);

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage($responseData['error_description']);

        $subscription->retry();
    }

    public function test_cancel()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            "status" => 1,
            "msg" => "Successfully cancelled the subscription",
            "data" => null
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription/cancel' => Http::response($responseData),
        ]);

        $subscription->cancel();

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Basic '.base64_encode('test_app_id:test_app_secret')) &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/oauth/token' &&
                $request['grant_type'] == 'client_credentials';
        });

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer pay-here-token') &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/subscription/cancel' &&
                $request['subscription_id'] == 'a_unique_subscription_id';
        });
    }

    public function test_it_throws_an_exception_when_not_eligible_for_cancelling()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            "status" => -1,
            "msg" => "Subscription is already cancelled.",
            "data" => null
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription/cancel' => Http::response($responseData, 422),
        ]);

        $this->expectException(NotAllowedToCancelException::class);
        $this->expectExceptionMessage($responseData['msg']);

        $subscription->cancel();
    }

    public function test_it_throws_an_exception_when_cancelling_weith_invalid_token()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $subscription->subscription_id = 'a_unique_subscription_id';
        $subscription->save();

        $responseData = [
            'error' => 'invalid_token',
            'error_description' => 'Invalid access token: e291493a-99a5-4177-9c8b-e8cd18ee9f85',
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription/cancel' => Http::response($responseData, 401),
        ]);

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage($responseData['error_description']);

        $subscription->cancel();
    }
}
