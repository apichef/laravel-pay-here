<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\DTO\Customer;
use ApiChef\PayHere\DTO\DeliveryDetails;
use ApiChef\PayHere\DTO\Item;
use ApiChef\PayHere\DTO\PaymentMethod;
use ApiChef\PayHere\DTO\PriceDetails;
use ApiChef\PayHere\DTO\SubscriptionDetails;
use ApiChef\PayHere\Exceptions\AuthorizationException;
use ApiChef\PayHere\Exceptions\InvalidTokenException;
use ApiChef\PayHere\Support\Facades\PayHere;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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

    public function test_getAllSubscriptions()
    {
        $responseData = [
            'status' => 1,
            'msg' => 'Found 1 subscriptions',
            'data' => [
                [
                    'subscription_id' => 320025071278,
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

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription' => Http::response($responseData),
        ]);

        $subscriptions = PayHere::getAllSubscriptions();

        /** @var SubscriptionDetails $subscriptionDetails */
        $subscriptionDetails = $subscriptions->first();

        $this->assertInstanceOf(SubscriptionDetails::class, $subscriptionDetails);
        $this->assertEquals($responseData['data'][0], $subscriptionDetails->data);

        $this->assertEquals($responseData['data'][0]['subscription_id'], $subscriptionDetails->subscriptionId);

        $this->assertEquals($responseData['data'][0]['order_id'], $subscriptionDetails->orderId);
        $this->assertEquals($responseData['data'][0]['date'], $subscriptionDetails->date->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(Carbon::class, $subscriptionDetails->date);
        $this->assertEquals($responseData['data'][0]['description'], $subscriptionDetails->description);
        $this->assertEquals($responseData['data'][0]['currency'], $subscriptionDetails->currency);
        $this->assertEquals($responseData['data'][0]['amount'], $subscriptionDetails->amount);

        $customerObject = $subscriptionDetails->getCustomer();
        $this->assertInstanceOf(Customer::class, $customerObject);
        $customer = $responseData['data'][0]['customer'];
        $this->assertEquals($customer, $subscriptionDetails->customer);

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

        $priceDetailsObject = $subscriptionDetails->getPriceDetails();
        $this->assertInstanceOf(PriceDetails::class, $priceDetailsObject);
        $amountDetail = $responseData['data'][0]['amount_detail'];
        $this->assertEquals($amountDetail, $subscriptionDetails->amountDetail);

        $this->assertEquals($amountDetail['currency'], $priceDetailsObject->currency);
        $this->assertEquals($amountDetail['gross'], $priceDetailsObject->gross);
        $this->assertEquals($amountDetail['fee'], $priceDetailsObject->fee);
        $this->assertEquals($amountDetail['net'], $priceDetailsObject->net);
        $this->assertEquals($amountDetail['exchange_rate'], $priceDetailsObject->exchangeRate);
        $this->assertEquals($amountDetail['exchange_from'], $priceDetailsObject->exchangeFrom);
        $this->assertEquals($amountDetail['exchange_to'], $priceDetailsObject->exchangeTo);

        $paymentMethodObject = $subscriptionDetails->getPaymentMethod();
        $this->assertInstanceOf(PaymentMethod::class, $paymentMethodObject);
        $paymentMethod = $responseData['data'][0]['payment_method'];
        $this->assertEquals($paymentMethod, $subscriptionDetails->paymentMethod);

        $this->assertEquals($paymentMethod['method'], $paymentMethodObject->method);
        $this->assertEquals($paymentMethod['card_customer_name'], $paymentMethodObject->nameOnCard);
        $this->assertEquals($paymentMethod['card_no'], $paymentMethodObject->cardNumber);

        $items = $subscriptionDetails->getItems();

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

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer pay-here-token') &&
                $request->url() == 'https://sandbox.payhere.lk/merchant/v1/subscription';
        });
    }

    public function test_getPaymentDetails_invalid_token()
    {
        $responseData = [
            "error" => "invalid_token",
            "error_description" => "Invalid access token: e291493a-99a5-4177-9c8b-e8cd18ee9f85"
        ];

        Http::fake([
            'sandbox.payhere.lk/merchant/v1/oauth/token' => Http::response([
                'access_token' => 'pay-here-token',
            ]),

            'sandbox.payhere.lk/merchant/v1/subscription' => Http::response($responseData, 401),
        ]);

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage($responseData['error_description']);

        PayHere::getAllSubscriptions();
    }
}
