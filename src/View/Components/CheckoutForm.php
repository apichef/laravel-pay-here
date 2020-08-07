<?php

namespace ApiChef\PayHere\View\Components;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\View\Component;

class CheckoutForm extends Component
{
    public Payment $payment;
    public string $merchantId;
    public string $formAction;
    public string $formClass;

    public function __construct(Payment $payment, string $formClass = '')
    {
        $this->merchantId = config('pay-here.merchant_credentials.id');
        $this->formAction = PayHere::checkoutUrl();
        $this->formClass = $formClass;
        $this->payment = $payment;
    }

    public function render()
    {
        return view('pay-here::checkout-form');
    }
}
