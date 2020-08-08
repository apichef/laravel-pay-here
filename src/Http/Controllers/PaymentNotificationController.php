<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Http\Requests\PaymentNotificationRequest;
use ApiChef\PayHere\Payment;
use Illuminate\Routing\Controller;

class PaymentNotificationController extends Controller
{
    public function __invoke(PaymentNotificationRequest $request)
    {
        $payment = Payment::findByOrderId($request->get('order_id'));
        $payment->status = $request->get('status_code');
        $payment->validated = $request->isValid($payment);
        $payment->summary = $request->all();
        $payment->save();
    }
}
