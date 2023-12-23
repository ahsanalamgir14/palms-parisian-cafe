<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = "reservations";
    protected $fillable = [
        'name',
        'phone',
        'email',
        'no_of_guests',
        'reservation_date_time',
    ];

    protected $casts = [
        'id'                => 'integer',
        'name'              => 'string',
        'phone'             => 'string',
        'email'             => 'string',
        'no_of_guests'      => 'integer',
        'reservation_date_time'  => 'datetime',
    ];
}
