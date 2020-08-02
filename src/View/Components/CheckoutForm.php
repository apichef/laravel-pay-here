<?php

namespace ApiChef\PayHere\View\Components;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\View\Component;

class CheckoutForm extends Component
{
    public string $formAction;

    public string $formClass;

    public Payment $payable;

    public function __construct(Payment $payment, string $formClass = '')
    {
        $this->merchantId = config('pay-here.merchant_credentials.id');
        $this->formAction = PayHere::checkoutUrl();
        $this->payable = $payment;
    }

    public function render()
    {
        return view('pay-here::checkout-form');
    }
}
