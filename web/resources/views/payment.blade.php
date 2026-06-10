<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $company['company_name'] }}</title>
    <link rel="icon" href="{{ $faviconLogo->faviconLogo }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/custom.css') }}">
</head>

<body>
<div class="py-14 px-4 w-full max-w-3xl mx-auto">
    <a href="{{ route('home') }}" class="block mx-auto w-36 mb-8">
        <img class="w-full" src="{{ $logo->logo }}" alt="logo">
    </a>

    <div id="loading-show" class="mx-auto w-80 {{ session()->has('error') ? 'hidden' : '' }}">
        <img class="w-full" src="{{ asset('images/required/payment-loading.gif') }}">
    </div>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative"
                 role="alert">
                <span class="block sm:inline">{{ $error }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer close-button">
                        <i class="lab lab-close-circle-line margin-top-5-px"></i>
                    </span>
            </div>
        @endforeach
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer close-button">
                    <i class="lab lab-close-circle-line margin-top-5-px"></i>
                </span>
        </div>
    @endif

    <form id="paymentForm" class="w-full" method="POST" action="{{ route('payment.store', ['order' => $order]) }}">
        @csrf
        <fieldset class="payment-fieldset hidden">
            @if (!blank($paymentGateways))
                @foreach ($paymentGateways as $paymentGateway)
                    @if(!$cashOnDelivery && $paymentGateway->slug === 'cash-on-delivery')
                        @continue
                    @elseif (!$credit && $paymentGateway->slug === 'credit')
                        @continue
                    @endif
                    <label class="payment-label" for="{{ $paymentGateway->slug }}">
                        <input class="paymentMethod" id="{{ $paymentGateway->slug }}" type="radio"
                               name="paymentMethod" value="{{ $paymentGateway->slug }}"
                               @if ($paymentMethod->slug == $paymentGateway->slug) checked @endif>
                        <img src="{{ $paymentGateway->image }}" alt="payment">
                        <span>{{ $paymentGateway->name }}</span>
                        @if ($paymentGateway->slug === 'credit')
                            <span>
                                    {{ $creditAmount }}
                                </span>
                        @endif
                    </label>
                @endforeach
            @endif
        </fieldset>

        @if (!blank($paymentGateways))
            @foreach ($paymentGateways as $paymentGateway)
                @if ($paymentGateway->misc !== null)
                    @if (!blank(json_decode($paymentGateway->misc)))
                        @if (!blank(json_decode($paymentGateway->misc)->input))
                            @foreach (json_decode($paymentGateway->misc)->input as $input)
                                @include('paymentGateways.' . str_replace('.blade.php', '', $input))
                            @endforeach
                        @endif
                    @endif
                @endif
            @endforeach
        @endif

        @if (!blank($paymentGateways))
            <button type="submit"
                    class="py-3 w-full rounded-3xl text-center text-base font-medium bg-primary text-white hidden"
                    id="confirmBtn">
                {{ __('all.label.confirm') }}
            </button>
        @endif

        <div id="backBtn"
             class="py-5 px-4 w-full max-w-3xl mx-auto flex flex-col items-center justify-center {{ !session()->has('error') ? 'hidden' : '' }}">
            <a class="text-primary" href="{{ url('/checkout/payment') }}">{{ __('all.label.back_to_payment') }}</a>
        </div>
    </form>
</div>

@php
    $jsGateway = [];
    $onClickGateway = [];
    $submitGateway = [];
@endphp
@if (!blank($paymentGateways))
    @foreach ($paymentGateways as $paymentGateway)
        @if ($paymentGateway->misc != null)
            @if (!blank(json_decode($paymentGateway->misc)))
                @if (!blank(json_decode($paymentGateway->misc)->js))
                    @foreach (json_decode($paymentGateway->misc)->js as $js)
                        @include('paymentGateways.' . str_replace('.blade.php', '', $js))
                    @endforeach
                @endif
                @if (!blank(json_decode($paymentGateway->misc)->input))
                    @if (isset(json_decode($paymentGateway->misc)->input[0]))
                        @php $jsGateway[$paymentGateway->slug] = isset(json_decode($paymentGateway->misc)->input[0]); @endphp
                    @endif
                @endif

                @if (!blank(json_decode($paymentGateway->misc)->onClick))
                    @if (isset(json_decode($paymentGateway->misc)->onClick) && json_decode($paymentGateway->misc)->onClick)
                        @php $onClickGateway[$paymentGateway->slug] = json_decode($paymentGateway->misc)->onClick; @endphp
                    @endif
                @endif

                @if (!blank(json_decode($paymentGateway->misc)->submit))
                    @if (isset(json_decode($paymentGateway->misc)->submit) && json_decode($paymentGateway->misc)->submit)
                        @php $submitGateway[$paymentGateway->slug] = json_decode($paymentGateway->misc)->submit; @endphp
                    @endif
                @endif
            @endif
        @endif
    @endforeach
@endif
@php
    $jsGateway = json_encode($jsGateway);
    $onClickGateway = json_encode($onClickGateway);
    $submitGateway = json_encode($submitGateway);
@endphp

<script src="{{ asset('lib/jquery-v3.7.1.min.js') }}"></script>
<script>
    const gateway        = <?= $jsGateway ?>;
    const onClickGateway = <?= $onClickGateway ?>;
    const submitGateway  = <?= $submitGateway ?>;
</script>
@if(!session()->has('error'))
    <script src="{{ asset('paymentGateways/payment.js') }}"></script>
@endif
</body>

</html>
