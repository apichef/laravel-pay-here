<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\Obfuscate\Support\Facades\Obfuscate;
use ApiChef\PayHere\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $payment = Payment::query()->find(Obfuscate::decode($request->get('order_id')));
        $payment->status = $request->get('status_code');
        $payment->validated = $payment->isTokenValid($request->get('md5sig'));
        $payment->save();
    }
}
