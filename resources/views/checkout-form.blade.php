<form method="POST" action="{{ $formAction }}" class="{{ $formClass }}">
    <input type="hidden" name="merchant_id" value="{{ $merchantId }}">
    <input type="hidden" name="return_url" value="{{ route('pay-here.success') }}">
    <input type="hidden" name="cancel_url" value="{{ route('pay-here.cancel') }}">
    <input type="hidden" name="notify_url" value="{{ route('pay-here.notify') }}">
    <input type="hidden" name="order_id" value="{{ $payment->getRouteKey() }}">
    <input type="hidden" name="hash" value="{{ $payment->getHash() }}">
    {{ $slot }}
</form>
