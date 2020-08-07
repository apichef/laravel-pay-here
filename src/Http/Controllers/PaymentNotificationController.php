<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $payment = Payment::findByOrderId($request->get('order_id'));
        $payment->status = $request->get('status_code');
        $payment->validated = $payment->isTokenValid($request->get('md5sig'));
        $payment->summary = array_merge($request->all(), ['hash' => $payment->hash]);
        $payment->save();
    }
}
