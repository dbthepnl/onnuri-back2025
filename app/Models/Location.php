<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'public',
        'building_name',
        'room_name',
        'room_type',
        'room_floor',
        'room_number',
        'location',
        'population',
        'gender',
        'created_at',
        'updated_at',
 ];
}
