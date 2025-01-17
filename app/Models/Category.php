<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Manipulations;
use Spatie\Image\Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Auth;


class Category extends Authenticatable implements HasMedia
{
     /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;
/**
      * The attributes that are mass assignable.
      *
      * @var list<string>
      */
      protected $fillable = [
        'name_ko',
        'name_en',
        'colorcode',
        'created_at',
        'updated_at',
 ];
}
