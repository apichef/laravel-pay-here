<?php

namespace ApiChef\PayHere\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class PaymentNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'merchant_id' => 'required',
            'order_id' => 'required',
            'payhere_amount' => 'required',
            'payhere_currency' => 'required',
            'status_code' => 'required',
            'md5sig' => 'required',
        ];
    }

    public function isValid(): bool
    {
        $secret = Str::upper(md5(config('pay-here.merchant_credentials.secret')));
        $merchantId = $this->get('merchant_id');
        $orderId = $this->get('order_id');
        $payhereAmount = $this->get('payhere_amount');
        $payhereCurrency = $this->get('payhere_currency');
        $statusCode = $this->get('status_code');
        $local_md5sig = Str::upper(md5($merchantId.$orderId.$payhereAmount.$payhereCurrency.$statusCode.$secret));

        return $local_md5sig === $this->get('md5sig');
    }
}
