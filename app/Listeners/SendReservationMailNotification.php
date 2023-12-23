<?php

namespace App\Listeners;

use App\Events\SendReservationMail;
use App\Services\ReservationMailNotificationBuilder;
use Illuminate\Support\Facades\Log;

class SendReservationMailNotification
{
    public function handle(SendReservationMail $event)
    {
        try{
            $reservationMailNotificationBuilder = new ReservationMailNotificationBuilder($event->info['name'], $event->info['phone'],  $event->info['email'],  $event->info['no_of_guests'],  $event->info['reservation_date_time']);
            $reservationMailNotificationBuilder->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
