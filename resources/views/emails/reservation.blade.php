@component('mail::message')
    # Reservation Notification

    Hello {{ $name }},

    {{ $message }}
    No of Guests: {{ $no_of_guests }}
    Reservation Date: {{ $reservation_date_time }}

    Thanks,
    {{ config('app.name') }}
@endcomponent