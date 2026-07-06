@php
    $parameters = ['paymentTransactionType' => null];

    if (isset($walletId) && !empty($walletId)) {
        $parameters['id'] = $walletId;
    }
@endphp

<ul class="nav nav-pills report-category-tabs">
    <li class="nav-item">
        <a class="nav-link {{ is_current_route($routeName, 'active', null, ['paymentTransactionType' => null]) }}"
           href="{{ route($routeName, $parameters) }}">{{ __('All') }}</a>
    </li>

    @foreach(config('commonconfig.payment_slug') as $key => $value)
        @php $parameters['paymentTransactionType'] = $key; @endphp
        <li class="nav-item">
            <a class="nav-link {{ is_current_route($routeName, 'active', null, ['paymentTransactionType' => $key]) }}"
               href="{{ route($routeName, $parameters) }}">{{ payment_status($value) }}</a>
        </li>
    @endforeach
</ul>
