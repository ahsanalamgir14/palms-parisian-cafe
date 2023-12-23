<?php

namespace App\Services;


use App\Models\NotificationAlert;
use App\Mail\ReservationMail;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationMailNotificationBuilder
{
    public string $name;
    public string $phone;
    public string $email;
    public int $no_of_guests;
    public mixed $reservation_date_time;

    public function __construct($name, $phone, $email, $no_of_guests, $reservation_date_time)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->no_of_guests = $no_of_guests;
        $this->reservation_date_time = $reservation_date_time;
    }

    public function send()
    {
        if (!blank($this->email)) {
            $notificationAlert = NotificationAlert::where(['language' => 'reservation_confirmation_message'])->first();
            try {
                Mail::to($this->email)->send(new ReservationMail($this->name, $this->no_of_guests, $this->reservation_date_time, $notificationAlert->mail_message));
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
        }
    }
}