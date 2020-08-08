<?php

namespace ApiChef\PayHere\View\Components;

use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class CheckoutForm extends Component
{
    public Model $payable;
    public string $merchantId;
    public string $formAction;
    public string $formClass;

    public function __construct(Model $payable, string $formClass = '')
    {
        $this->merchantId = config('pay-here.merchant_credentials.id');
        $this->formAction = PayHere::checkoutUrl();
        $this->formClass = $formClass;
        $this->payable = $payable;
    }

    public function render()
    {
        return view('pay-here::checkout-form');
    }
}
