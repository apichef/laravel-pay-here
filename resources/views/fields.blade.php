<input type="hidden" name="merchant_id" value="{{ $merchantId }}">
<input type="hidden" name="return_url" value="{{ $successUrl }}">
<input type="hidden" name="cancel_url" value="{{ $cancelUrl }}">
<input type="hidden" name="notify_url" value="{{ route('pay-here.notify') }}">
<input type="hidden" name="order_id" value="{{ $payment->getRouteKey() }}">
<input type="hidden" name="items" value="{{ $itemDescription }}"><br>
<input type="hidden" name="currency" value="{{ $payment->currency }}">
<input type="hidden" name="amount" value="{{ $payment->amount }}">
