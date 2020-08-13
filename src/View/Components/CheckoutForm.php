<?php

namespace ApiChef\PayHere\View\Components;

use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class CheckoutForm extends Component
{
    public $payable;
    public $merchantId;
    public $formAction;
    public $formClass;
    public $successUrl;
    public $cancelledUrl;

    public function __construct(Model $payable, string $successUrl, string $cancelledUrl, string $formClass = '')
    {
        $this->merchantId = config('pay-here.merchant_credentials.id');
        $this->formAction = PayHere::checkoutUrl();
        $this->formClass = $formClass;
        $this->payable = $payable;
        $this->successUrl = $successUrl;
        $this->cancelledUrl = $cancelledUrl;
    }

    public function render()
    {
        return view('pay-here::checkout-form');
    }
}
