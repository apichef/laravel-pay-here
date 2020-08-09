<?php

namespace ApiChef\PayHere;

use ApiChef\Obfuscate\Obfuscatable;
use ApiChef\Obfuscate\Support\Facades\Obfuscate;
use ApiChef\PayHere\Support\Facades\PayHere as PayHereFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Subscription extends Model
{
    use Obfuscatable;
    use Payable;

    protected $casts = [
        'summary' => 'array',
    ];

    // relationships

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    // helpers

    public static function make(
        Model $subscribable,
        Model $buyer,
        string $recurrence,
        string $duration,
        float $price,
        string $currency = PayHere::CURRENCY_LKR
    ): self {
        self::validateCurrency($currency);

        $subscription = new self();
        $subscription->amount = $price;
        $subscription->currency = $currency;
        $subscription->recurrence = $recurrence;
        $subscription->duration = $duration;
        $subscription->subscribable()->associate($subscribable);
        $subscription->payer()->associate($buyer);
        $subscription->save();

        return $subscription;
    }

    public static function findByOrderId($orderId): self
    {
        return Subscription::query()
            ->findOrFail(Obfuscate::decode($orderId));
    }

    public function getPayments(): Collection
    {
        return PayHereFacade::getSubscriptionPayments($this);
    }
}
