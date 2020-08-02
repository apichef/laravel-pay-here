<?php

namespace ApiChef\PayHere;

use ApiChef\Obfuscate\Obfuscatable;
use ApiChef\Obfuscate\Support\Facades\Obfuscate;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use Obfuscatable;

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

    public static function make(Model $item, Model $buyer, float $price, string $currency = PayHere::CURRENCY_LKR): self
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
        return $this->getHash() === $token;
    }

    public function isPaid(): bool
    {
        return $this->status == 2;
    }

    public function getHash()
    {
        $secret = Str::upper(md5(config('pay-here.merchant_credentials.secret')));
        $merchantId = config('pay-here.merchant_credentials.id');

        return Str::upper(md5("{$merchantId}{$this->id}{$this->amount}{$this->currency}{$secret}"));
    }

    public static function findByOrderId($orderId): self
    {
        return Payment::query()
            ->find(Obfuscate::decode($orderId));
    }

    public function refreshStatus(): self
    {
        $this->status = PayHere::getOrderDetails($this->getRouteKey())->status;
        $this->save();

        return $this;
    }
}
