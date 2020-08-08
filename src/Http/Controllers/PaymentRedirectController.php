<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentRedirectController extends Controller
{
    public function success(Request $request)
    {
        $payment = Payment::findByOrderId($request->get('order_id'));

        return redirect()
            ->route(config('pay-here.routes_name.payment_success'), $payment);
    }

    public function canceled(Request $request)
    {
        $payment = Payment::findByOrderId($request->get('order_id'));

        return redirect()
            ->route(config('pay-here.routes_name.payment_canceled'), $payment);
    }
}
