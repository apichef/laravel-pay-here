<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Http\Requests\CheckoutRequest;
use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function __invoke(CheckoutRequest $request)
    {
        $payment = Payment::make(
            $request->getPayable(),
            $request->user(),
            $request->get('amount'),
            $request->get('country')
        );

        return Http::asForm()
            ->post(PayHere::checkoutUrl(), array_merge($request->getDataToPost(), [
                'merchant_id' => config('pay-here.merchant_credentials.id'),
                'return_url' => route('pay-here.success'),
                'cancel_url' => route('pay-here.cancel'),
                'notify_url' => route('pay-here.notify'),
                'order_id' => $payment->getRouteKey(),
                'hash' => $payment->getHash(),
            ]));
    }
}
