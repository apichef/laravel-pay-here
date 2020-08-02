<?php

namespace ApiChef\PayHere;

use ApiChef\Obfuscate\Obfuscatable;
use ApiChef\Obfuscate\Support\Facades\Obfuscate;
use ApiChef\PayHere\Exceptions\UnsupportedCurrencyException;
use ApiChef\PayHere\Support\Facades\PayHere as PayHereFacades;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use Obfuscatable;

    public function getConnectionName()
    {
        return config('pay-here.database_connection');
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function payer(): MorphTo
    {
        return $this->morphTo();
    }

    public static function make(Model $item, Model $buyer, float $price, string $currency = PayHere::CURRENCY_LKR): self
    {
        if (! in_array($currency, PayHereFacades::allowedCurrencies())) {
            throw new UnsupportedCurrencyException();
        }

        $payment = new self();
        $payment->amount = $price;
        $payment->currency = $currency;
        $payment->payable()->associate($item);
        $payment->payer()->associate($buyer);
        $payment->save();

        return $payment;
    }

    public function isTokenValid(string $token): bool
    {
        return $this->getHash() === $token;
    }

    public function isPaid(): bool
    {
        return $this->status > 0;
    }

    public function getHash(): string
    {
        $secret = Str::upper(md5(config('pay-here.merchant_credentials.secret')));
        $merchantId = config('pay-here.merchant_credentials.id');

        return Str::upper(md5("{$merchantId}{$this->getRouteKey()}{$this->amount}{$this->currency}{$secret}"));
    }

    public static function findByOrderId($orderId): self
    {
        return Payment::query()
            ->findOrFail(Obfuscate::decode($orderId));
    }

    public function refreshStatus(): self
    {
        if ($this->status === 0) {
            $this->status = PayHereFacades::getOrderDetails($this->getRouteKey())->status;
            $this->save();
        }

        return $this;
    }

    public function scopePaidBy(Builder $query, Model $payer): Builder
    {
        return $query
            ->where('payer_type', get_class($payer))
            ->where('payer_id', $payer->getKey());
    }

    public function scopePaidFor(Builder $query, Model $payable): Builder
    {
        return $query
            ->where('payable_type', get_class($payable))
            ->where('payable_id', $payable->getKey())
            ->where('status', '>', 0);
    }

    public function scopeSuccess(Builder $query): Builder
    {
        return $query
            ->where('status', '>', 0);
    }
}
