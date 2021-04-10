<?php

namespace ApiChef\PayHere\View\Components;

use ApiChef\PayHere\Items;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class CheckoutForm extends Component
{
    public Model $payable;
    public string $merchantId;
    public string $formAction;
    public string $formClass;
    public string $successUrl;
    public string $cancelledUrl;
    private Items $items;

    public function __construct(
        Model $payable,
        string $successUrl,
        string $cancelledUrl,
        string $formClass = '',
        Items $items = null
    ) {
        $this->merchantId = config('pay-here.merchant_credentials.id');
        $this->formAction = PayHere::checkoutUrl();
        $this->formClass = $formClass;
        $this->payable = $payable;
        $this->successUrl = $successUrl;
        $this->cancelledUrl = $cancelledUrl;
        $this->items = $items ?: new Items();
    }

    public function render()
    {
        return view('pay-here::checkout-form');
    }
}
