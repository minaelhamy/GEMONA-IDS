@component('mail::message')
    # Verify Email

    Your code is {{ $pin }}

    Please do not share your One Time Code With Anyone.

    Thanks,
    {{ config('app.name') }}
@endcomponent
