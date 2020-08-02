<?php

namespace ApiChef\PayHere;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class PayHere
{
    const BASE_URL_PRODUCTION = 'https://www.payhere.lk/';
    const BASE_URL_SANDBOX = 'https://sandbox.payhere.lk/';

    const URI_GENERATE_TOKEN = 'merchant/v1/oauth/token';
    const URI_RETRIEVAL_ORDER_DETAIL = 'merchant/v1/payment/search';

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

        throw new AuthenticationException("Unable to authenticate PayHere");
    }

    private function getUrl(string $uri): string
    {
        if (App::environment() === 'production') {
            return self::BASE_URL_PRODUCTION . $uri;
        }

        return self::BASE_URL_SANDBOX.$uri;
    }

    public function getOrderDetails(string $orderId): OrderDetails
    {
        if ($this->accessToken === null) {
            $this->authenticate();
        }

        $data = Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
        ])
        ->asJson()
        ->get($this->getUrl(self::URI_RETRIEVAL_ORDER_DETAIL), [
            'order_id' => $orderId,
        ])->json();

        return new OrderDetails($data['status'], $data['msg'], $data['data']);
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
