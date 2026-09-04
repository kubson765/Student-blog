@component('mail::message')
# Verify Your Email Address

Hello {{ $user->name ?? '' }}!

Thanks for creating an account. Please verify your email address by clicking the button below.

@component('mail::button', ['url' => $actionUrl, 'color' => 'primary'])
Verify Email Address
@endcomponent

If you did not create an account, no further action is required.

Regards,<br>
{{ config('app.name') }}

@component('mail::subcopy')
If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser: [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endcomponent