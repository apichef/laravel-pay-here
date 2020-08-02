<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SuccessRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $payment = Payment::findByOrderId($request->get('order_id'));

        return redirect()
            ->route(config('pay-here.routes_name.payment_success'), $payment->refreshStatus());
    }
}
