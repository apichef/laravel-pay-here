<?php

namespace ApiChef\PayHere;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class PayHere
{
    private $accessToken = null;

    private function authenticate(): self
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.base64_encode(config('pay-here.business_app_credentials.id').':'.config('pay-here.business_app_credentials.secret')),
        ])->post($this->getUrl('merchant/v1/oauth/token'), [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $this->accessToken = $response->object()->access_token;

            return $this;
        }

        throw new AuthenticationException("Unable to authenticate PayHere");
    }

    private function getUrl(string $uri)
    {
        $base = App::environment() === 'production' ? 'https://www.payhere.lk/' : 'https://sandbox.payhere.lk/';

        return $base.$uri;
    }

    public function getOrderDetails(string $orderId)
    {
        if ($this->accessToken === null) {
            $this->authenticate();
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer '.$this->accessToken
        ])->asJson()->get($this->getUrl('merchant/v1/payment/search'), [
            'order_id' => $orderId
        ])->object();
    }
}
