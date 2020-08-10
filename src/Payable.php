<?php

namespace ApiChef\PayHere;

use ApiChef\PayHere\DTO\PaymentDetails;
use ApiChef\PayHere\Exceptions\UnsupportedCurrencyException;
use ApiChef\PayHere\Support\Facades\PayHere as PayHereFacades;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

trait Payable
{
    // overrides

    public function getConnectionName()
    {
        return config('pay-here.database_connection');
    }

    // relationships

    public function payer(): MorphTo
    {
        return $this->morphTo();
    }

    // accessors

    public function getAmountAttribute($value)
    {
        return number_format((float) $value, 2, '.', '');
    }

    public function getHashAttribute(): string
    {
        $secret = Str::upper(md5(config('pay-here.merchant_credentials.secret')));
        $merchantId = config('pay-here.merchant_credentials.id');

        return Str::upper(md5("{$merchantId}{$this->getRouteKey()}{$this->amount}{$this->currency}{$secret}"));
    }

    // scopes

    public function scopePaidBy(Builder $query, Model $payer): Builder
    {
        return $query
            ->where('payer_type', get_class($payer))
            ->where('payer_id', $payer->getKey());
    }

    // helpers

    public function getPaymentDetails(): PaymentDetails
    {
        return PayHereFacades::getPaymentDetails($this->getRouteKey());
    }

    private static function validateCurrency(string $currency): void
    {
        if (! in_array($currency, PayHereFacades::allowedCurrencies())) {
            throw new UnsupportedCurrencyException();
        }
    }
}
