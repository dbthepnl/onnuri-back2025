<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{

    protected $guarded = ['id'];
    
    protected $fillable = [
        'id',
        'public',
        'reservation_type',
        'order',
        'name', 
        'home_phone',
        'phone', 
        'email',
        'password',
        'church',
        'church_phone',
        'pastor_name',
        'church_address',
        'organization',
        'leader',
        'event_name',
        'office_phone',
        'address',
        'room_worship_type',
        'room_reservation',
        'worship_reservation',
        'cafeteria_reservation',
        'created_at',
        'updated_at'
      ];
}
