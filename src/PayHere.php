<?php

namespace ApiChef\PayHere;

use ApiChef\PayHere\DTO\PaymentDetails;
use ApiChef\PayHere\DTO\SubscriptionDetails;
use ApiChef\PayHere\Exceptions\AuthorizationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PayHere
{
    const URI_GENERATE_TOKEN = 'merchant/v1/oauth/token';
    const URI_RETRIEVAL_ORDER_DETAIL = 'merchant/v1/payment/search';
    const URI_ALL_SUBSCRIPTIONS = 'merchant/v1/subscription';

    const CURRENCY_LKR = 'LKR';
    const CURRENCY_USD = 'USD';
    const CURRENCY_GBP = 'GBP';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_AUD = 'AUD';

    private $accessToken = null;

    private function authenticate(): self
    {
        $appId = config('pay-here.business_app_credentials.id');
        $appSecret = config('pay-here.business_app_credentials.secret');
        $token = base64_encode("{$appId}:{$appSecret}");

        $response = Http::withHeaders([
            'Authorization' => "Basic {$token}",
        ])->asForm()->post($this->getUrl(self::URI_GENERATE_TOKEN), [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $this->accessToken = $response->object()->access_token;

            return $this;
        }

        throw new AuthorizationException();
    }

    public function getToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $this->authenticate();

        return $this->accessToken;
    }

    private function getUrl(string $uri): string
    {
        return config('pay-here.base_url').$uri;
    }

    private function getRequest(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->getToken()}",
        ])->asJson();
    }

    public function getPaymentDetails(string $orderId): PaymentDetails
    {
        $data = $this->getRequest()
        ->get($this->getUrl(self::URI_RETRIEVAL_ORDER_DETAIL), [
            'order_id' => $orderId,
        ])->json();

        return new PaymentDetails($data['data'][0]);
    }

    public function getAllSubscriptions(): Collection
    {
        $data = $this->getRequest()
            ->get($this->getUrl(self::URI_ALL_SUBSCRIPTIONS))
            ->json();

        return collect($data['data'])->map(function ($subscription) {
            return new SubscriptionDetails($subscription);
        });
    }

    public function checkoutUrl(): string
    {
        return $this->getUrl('pay/checkout');
    }

    public function allowedCurrencies(): array
    {
        return [
            self::CURRENCY_LKR,
            self::CURRENCY_AUD,
            self::CURRENCY_EUR,
            self::CURRENCY_GBP,
            self::CURRENCY_USD,
        ];
    }
}
