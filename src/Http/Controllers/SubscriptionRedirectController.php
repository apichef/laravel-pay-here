<?php

namespace ApiChef\PayHere\Http\Controllers;

use ApiChef\PayHere\Subscription;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubscriptionRedirectController extends Controller
{
    public function success(Request $request)
    {
        $subscription = Subscription::findByOrderId($request->get('order_id'));

        return redirect()
            ->route(config('pay-here.routes_name.subscription_success'), $subscription);
    }

    public function canceled(Request $request)
    {
        $subscription = Subscription::findByOrderId($request->get('order_id'));

        return redirect()
            ->route(config('pay-here.routes_name.subscription_canceled'), $subscription);
    }
}
