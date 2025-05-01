@component('mail::message')
# Welcome to Court Booking System

Dear {{ $name }},

Your court booking system account has been approved! You can now access your dashboard using the following credentials:

**Email:** {{ $email }}
**Password:** {{ $password }}
**Domain:** {{ $domain }}

You can access your dashboard at: https://{{ $domain }}

Please change your password after your first login for security purposes.

As an approved tenant, you can now:
- Add secondary admins (SK, Secretary, Captain)
- Manage users
- Handle court bookings
- And more!

@component('mail::button', ['url' => 'https://' . $domain])
Access Your Dashboard
@endcomponent

If you have any questions, please don't hesitate to contact our support team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 