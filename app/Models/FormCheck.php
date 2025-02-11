<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormCheck extends Model
{
    protected $fillable = [
        'user_id',
        'board_id',
        'step_id',
        'form_id',
        'cardinal_id',
        'success'
 ];
}
