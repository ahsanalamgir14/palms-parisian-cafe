<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public string $name;
    public int $no_of_guests;
    public mixed $reservation_date_time;
    public mixed $message;

    public function __construct($name, $no_of_guests, $reservation_date_time, $message)
    {
        $this->name    = $name;
        $this->no_of_guests = $no_of_guests;
        $this->reservation_date_time = $reservation_date_time;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject("Reservation Notification")->markdown('emails.reservation');
    }
}
