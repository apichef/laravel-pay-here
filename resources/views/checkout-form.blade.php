<form method="POST" action="{{ $formAction }}" class="{{ $formClass }}">
    @csrf
    <input type="hidden" name="merchant_id" value="{{ $merchantId }}">
    <input type="hidden" name="return_url" value="{{ route('pay-here.success') }}">
    <input type="hidden" name="cancel_url" value="{{ route('pay-here.cancel') }}">
    <input type="hidden" name="notify_url" value="{{ route('pay-here.notify') }}">
    <input type="hidden" name="order_id" value="{{ $payment->getRouteKey() }}">
    {{ $slot }}
</form>
