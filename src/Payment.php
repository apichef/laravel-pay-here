<?php

namespace ApiChef\PayHere;

use ApiChef\Obfuscate\Obfuscatable;
use ApiChef\Obfuscate\Support\Facades\Obfuscate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use Obfuscatable;
    use Payable;

    protected $casts = [
        'summary' => 'array',
    ];

    // relationships

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    // scopes

    public function scopePaidFor(Builder $query, Model $payable): Builder
    {
        return $query
            ->where('payable_type', get_class($payable))
            ->where('payable_id', $payable->getKey());
    }

    public function scopeSuccess(Builder $query): Builder
    {
        return $query->where('status', 2);
    }

    // helpers

    public static function make(
        Model $item,
        Model $buyer,
        float $price,
        string $currency = PayHere::CURRENCY_LKR
    ): self {
        self::validateCurrency($currency);

        $payment = new self();
        $payment->amount = $price;
        $payment->currency = $currency;
        $payment->payable()->associate($item);
        $payment->payer()->associate($buyer);
        $payment->save();

        return $payment;
    }

    public function isPaid(): bool
    {
        return $this->status > 0;
    }

    public static function findByOrderId($orderId): self
    {
        return Payment::query()
            ->findOrFail(Obfuscate::decode((int) $orderId));
    }
}
