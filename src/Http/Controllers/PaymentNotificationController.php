<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Http\Requests\PaymentNotificationRequest;
use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Subscription;
use Illuminate\Routing\Controller;

class PaymentNotificationController extends Controller
{
    public function __invoke(PaymentNotificationRequest $request)
    {
        if ($request->isSubscription()) {
            return $this->updateSubscription($request);
        }

        return $this->updatePayment($request);
    }

    private function updateSubscription(PaymentNotificationRequest $request): bool
    {
        $subscription = Subscription::findByOrderId($request->get('order_id'));
        $subscription->payment_id = $request->get('payment_id');
        $subscription->subscription_id = $request->get('subscription_id');
        $subscription->status = $request->get('status_code');
        $subscription->validated = $request->isValid($subscription);
        $subscription->summary = $request->all();
        $subscription->recurrence_status = $request->get('item_rec_status');
        $subscription->next_recurrence_date = $request->get('item_rec_date_next');
        $subscription->times_paid = $request->get('item_rec_install_paid');

        return $subscription->save();
    }

    private function updatePayment(PaymentNotificationRequest $request): bool
    {
        $payment = Payment::findByOrderId($request->get('order_id'));
        $payment->payment_id = $request->get('payment_id');
        $payment->status = $request->get('status_code');
        $payment->validated = $request->isValid($payment);
        $payment->summary = $request->all();

        return $payment->save();
    }
}
