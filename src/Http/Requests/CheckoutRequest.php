<?php

namespace Apichef\PayHere\Http\Requests;

use ApiChef\PayHere\Rules\Payable;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'payable' => [
                'required',
                new Payable(),
            ],

            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'title' => 'required',
            'currency' => [
                'required',
                Rule::in(PayHere::allowedCurrencies()),
            ],
            'amount' => 'required|integer|min:1',

            'delivery_address' => 'required_with:delivery_city,delivery_country',
            'delivery_city' => 'required_with:delivery_address,delivery_country',
            'delivery_country' => 'required_with:delivery_address,delivery_city',

            'items.*.id' => 'required_with:items.*.name,items.*.unit,items.*.quantity',
            'items.*.name' => 'required_with:items.*.id,items.*.unit,items.*.quantity',
            'items.*.unit' => 'required_with:items.*.id,items.*.name,items.*.quantity',
            'items.*.quantity' => 'required_with:items.*.id,items.*.name,items.*.unit|integer|min:1',
        ];
    }

    public function getPayable(): Model
    {
        return call_user_func("{$this->input('payable.type')}::query()")
            ->find($this->input('payable.id'));
    }

    public function getDataToPost(): array
    {
        $data = $this->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
            'city',
            'country',
            'amount',
        ]);

        $data['item'] = $this->get('title');

        return $data;
    }
}
