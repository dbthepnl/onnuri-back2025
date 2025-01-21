<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormElement extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'id',
        'form_id',
        'order',
        'element', 
        'element_name',
        'tag', 
        'required',
        'label',
        'values',
        'sub_text',
        'created_at',
        'updated_at'
      ];
}
