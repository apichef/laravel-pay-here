<?php

namespace ApiChef\PayHere;

use ApiChef\Obfuscate\Obfuscatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use Obfuscatable;

    public const CURRENCY_LKR = 'LKR';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_GBP = 'GBP';
    public const CURRENCY_AUD = 'AUD';

    public function getConnectionName()
    {
        return config('pay-here.database_connection');
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function payer()
    {
        return $this->morphTo();
    }

    public static function make(Model $item, Model $buyer, float $price, string $currency = self::CURRENCY_LKR): self
    {
        $payment = new self();
        $payment->amount = $price;
        $payment->currency = $currency;
        $payment->payable()->associate($item);
        $payment->payer()->associate($buyer);
        $payment->save();

        return $payment;
    }

    public function isTokenValid($token): bool
    {
        $secret = Str::upper(md5(config('pay-here.merchant_credentials.secret')));
        $merchantId = config('pay-here.merchant_credentials.id');

        return Str::upper(md5("{$merchantId}{$this->id}{$this->amount}{$this->currency}{$secret}")) === $token;
    }

    public function isPaid(): bool
    {
        return $this->status == 2;
    }
}
