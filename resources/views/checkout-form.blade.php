<form method="POST" action="{{ $formAction }}" class="{{ $formClass }}">
    <input type="hidden" name="merchant_id" value="{{ $merchantId }}">
    <input type="hidden" name="notify_url" value="{{ route('pay-here.notify') }}">
    <input type="hidden" name="order_id" value="{{ $payable->getRouteKey() }}">
    <input type="hidden" name="amount" value="{{ $payable->amount }}">
    <input type="hidden" name="currency" value="{{ $payable->currency }}">
    <input type="hidden" name="hash" value="{{ $payable->hash }}">
    <input type="hidden" name="return_url" value="{{ $successUrl }}">
    <input type="hidden" name="cancel_url" value="{{ $cancelledUrl }}">
    @if($payable instanceof \ApiChef\PayHere\Subscription)
        <input type="hidden" name="recurrence" value="{{ $payable->recurrence }}">
        <input type="hidden" name="duration" value="{{ $payable->duration }}">
    @endif
    {{ $slot }}
</form>
