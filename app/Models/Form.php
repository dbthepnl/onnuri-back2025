<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    protected $fillable = [
        'id',
        'user_id',
        'category_id',
        'title', 
        'public',
        'order', 
        'caption',
        'member_type',
        'created_at',
        'updated_at'
      ];
    
}
