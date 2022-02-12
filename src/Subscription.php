<?php

namespace ApiChef\PayHere;

use ApiChef\Obfuscate\Obfuscatable;
use ApiChef\Obfuscate\Support\Facades\Obfuscate;
use ApiChef\PayHere\Support\Facades\PayHere as PayHereFacade;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Subscription extends Model
{
    use Obfuscatable;
    use Payable;
    use HasFactory;

    protected $casts = [
        'summary' => 'array',
    ];

    // overrides

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }

    // relationships

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    // scopes

    public function scopeSubscribedTo(Builder $query, Model $subscribable): Builder
    {
        return $query
            ->where('subscribable_type', get_class($subscribable))
            ->where('subscribable_id', $subscribable->getKey());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('recurrence_status', 0);
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
            ->findOrFail(Obfuscate::decode((int) $orderId));
    }

    public function getPayments(): Collection
    {
        return PayHereFacade::getSubscriptionPayments($this);
    }

    public function retry(): bool
    {
        return PayHereFacade::retrySubscription($this);
    }

    public function cancel()
    {
        return PayHereFacade::cancelSubscription($this);
    }
}
