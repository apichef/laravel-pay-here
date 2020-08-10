<?php

namespace ApiChef\PayHere;

use ApiChef\PayHere\DTO\PaymentDetails;
use ApiChef\PayHere\DTO\SubscriptionDetails;
use ApiChef\PayHere\Exceptions\AuthorizationException;
use ApiChef\PayHere\Exceptions\InvalidTokenException;
use ApiChef\PayHere\Exceptions\NotAllowedToCancelException;
use ApiChef\PayHere\Exceptions\NotEligibleForRetryingException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PayHere
{
    const URI_GENERATE_TOKEN = 'merchant/v1/oauth/token';
    const URI_RETRIEVAL_ORDER_DETAIL = 'merchant/v1/payment/search';
    const URI_ALL_SUBSCRIPTIONS = 'merchant/v1/subscription';
    const URI_RETRY_SUBSCRIPTIONS = 'merchant/v1/subscription/retry';
    const URI_CANCEL_SUBSCRIPTIONS = 'merchant/v1/subscription/cancel';

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
        $response = $this->getRequest()
            ->get($this->getUrl(self::URI_RETRIEVAL_ORDER_DETAIL), [
                'order_id' => $orderId,
            ]);

        if (! $response->successful()) {
            $this->handleError($response);
        }

        return new PaymentDetails($response->json()['data'][0]);
    }

    public function getAllSubscriptions(): Collection
    {
        $response = $this->getRequest()
            ->get($this->getUrl(self::URI_ALL_SUBSCRIPTIONS));

        if (! $response->successful()) {
            $this->handleError($response);
        }

        return collect($response->json()['data'])->map(function ($subscription) {
            return new SubscriptionDetails($subscription);
        });
    }

    public function getSubscriptionPayments(Subscription $subscription): Collection
    {
        $response = $this->getRequest()
            ->get($this->getUrl("merchant/v1/subscription/{$subscription->subscription_id}/payments"));

        if (! $response->successful()) {
            $this->handleError($response);
        }

        return collect($response->json()['data'])->map(function ($subscription) {
            return new PaymentDetails($subscription);
        });
    }

    public function retrySubscription(Subscription $subscription): bool
    {
        $response = $this->getRequest()
            ->post($this->getUrl(self::URI_RETRY_SUBSCRIPTIONS), [
                'subscription_id' => $subscription->subscription_id,
            ]);

        $data = $response->json();

        if (! $response->successful()) {
            if (array_key_exists('status', $data) && $data['status'] === -1) {
                throw new NotEligibleForRetryingException($data['msg']);
            }

            $this->handleError($response);
        }

        return true;
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        $response = $this->getRequest()
            ->post($this->getUrl(self::URI_CANCEL_SUBSCRIPTIONS), [
                'subscription_id' => $subscription->subscription_id,
            ]);

        $data = $response->json();

        if (! $response->successful()) {
            if (array_key_exists('status', $data) && $data['status'] === -1) {
                throw new NotAllowedToCancelException($data['msg']);
            }

            $this->handleError($response);
        }

        return true;
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

    private function handleError(Response $response)
    {
        $error = $response->json();

        if (array_key_exists('error', $error) && $error['error'] === 'invalid_token') {
            throw new InvalidTokenException($error['error_description']);
        }

        throw new \RuntimeException();
    }
}
