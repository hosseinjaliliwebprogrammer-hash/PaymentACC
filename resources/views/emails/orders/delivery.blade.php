@component('mail::message')
# Your Account Details

Dear {{ $order->user->name ?? 'Customer' }},

Your order (ID: {{ $order->id }}) has been **successfully completed**.  
Below you will find your account credentials:

@component('mail::panel')
**Server:** {{ $order->delivery_server ?? '-' }}  
**Username:** {{ $order->delivery_username ?? '-' }}  
**Password:** {{ $order->delivery_password ?? '-' }}
@endcomponent

@if($order->delivery_notes)
**Additional notes:**

{{ $order->delivery_notes }}
@endif

If you experience any issues logging in or have questions, simply reply to this email and our support team will assist you.

Thank you for choosing {{ config('app.name') }}!

@endcomponent
