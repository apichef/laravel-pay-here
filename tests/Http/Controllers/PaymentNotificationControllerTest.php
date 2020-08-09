<?php

namespace ApiChef\PayHere\Tests\Http\Controllers;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\TestCase;

class PaymentNotificationControllerTest extends TestCase
{
    public function test_it_updated_payment()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();

        $data = [
            'merchant_id' => 'test_merchant_id',
            'order_id' => $payment->getRouteKey(),
            'payment_id' => 'a_unique_payment_id',
            'payhere_amount' => '100.00',
            'payhere_currency' => 'LKR',
            'status_code' => 2,
            'md5sig' => 'D6AD18771E309FDE75C14BCFB8513359',
            'custom_1' => 'data_custom_1',
            'custom_2' => 'data_custom_2',
            'method' => 'VISA',
            'status_message' => 'Successfully completed the payment.',

            'card_holder_name' => 'Saman Kumara',
            'card_no' => '************4564',
            'card_expiry' => '1222',
        ];

        $this->post(route('pay-here.notify'), $data);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_id' => $data['payment_id'],
            'amount' => $data['payhere_amount'],
            'status' => $data['status_code'],
            'currency' => $data['payhere_currency'],
            'validated' => true,
        ]);
    }

    public function test_it_updated_subscription()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();

        $data = [
            'merchant_id' => 'test_merchant_id',
            'order_id' => $subscription->getRouteKey(),
            'payment_id' => 'a_unique_payment_id',
            'subscription_id' => 'a_unique_subscription_id',
            'payhere_amount' => '100.00',
            'payhere_currency' => 'LKR',
            'status_code' => 2,
            'md5sig' => 'D6AD18771E309FDE75C14BCFB8513359',
            'custom_1' => 'data_custom_1',
            'custom_2' => 'data_custom_2',
            'method' => 'VISA',
            'status_message' => 'Successfully completed the payment.',

            'recurring' => 1,
            'message_type' => 'AUTHORIZATION_SUCCESS',

            'item_recurrence' => '1 Month',
            'item_duration' => 'Forever',
            'item_rec_status' => 0,
            'item_rec_date_next' => '2020-02-02',
            'item_rec_install_paid' => 1,

            'card_holder_name' => 'Saman Kumara',
            'card_no' => '************4564',
            'card_expiry' => '1222',
        ];

        $this->post(route('pay-here.notify'), $data);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'payment_id' => $data['payment_id'],
            'amount' => $data['payhere_amount'],
            'status' => $data['status_code'],
            'currency' => $data['payhere_currency'],
            'validated' => true,

            'recurrence' => $data['item_recurrence'],
            'duration' => $data['item_duration'],
            'recurrence_status' => $data['item_rec_status'],
            'next_recurrence_date' => $data['item_rec_date_next'],
            'times_paid' => $data['item_rec_install_paid'],
        ]);
    }

    public function test_it_sets_validated_to_false_when_the_hash_dose_not_match()
    {
        /** @var Payment $payment */
        $payment = factory(Payment::class)->create();

        $data = [
            'merchant_id' => 'test_merchant_id',
            'order_id' => $payment->getRouteKey(),
            'payment_id' => 'a_unique_payment_id',
            'payhere_amount' => '100.00',
            'payhere_currency' => 'LKR',
            'status_code' => 2,
            'md5sig' => 'INVALID_HASH',
            'custom_1' => 'data_custom_1',
            'custom_2' => 'data_custom_2',
            'method' => 'VISA',
            'status_message' => 'Successfully completed the payment.',

            'card_holder_name' => 'Saman Kumara',
            'card_no' => '************4564',
            'card_expiry' => '1222',
        ];

        $this->post(route('pay-here.notify'), $data);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'validated' => false,
        ]);
    }
}
