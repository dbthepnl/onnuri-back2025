<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

class Donation extends Authenticatable implements HasMedia
{
     /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;

     /**
      * The attributes that are mass assignable.
      *
      * @var list<string>
      */
      protected $fillable = [
        'id',
        'user_id',
        'title', 
        'public',
        'order', 
        'board',
        'goal_price',
        'current_price',
        'content',
        'created_at',
        'updated_at'
      ];

      /**
      * The attributes that should be hidden for serialization.
      *
      * @var list<string>
      */
     protected $hidden = [

     ];
 
     /**
      * Get the attributes that should be cast.
      *
      * @return array<string, string>
      */
     protected function casts(): array
     {
         return [

         ];
     }
 
     public function registerMediaConversions(Media $media = null): void
     {
        // $this->addMediaConversion('preview')->fit(Manipulations::FIT_CROP, 150, 150)->nonQueued();
     }
 
     public function getImgAttribute()
     {
         if ($this->hasMedia('d_photo')) {
             $img = $this->getMedia('d_photo')->last();
             return $img->getUrl();
         }
 
         return null;
     }

     public function getFiles()
     {
         if ($this->hasMedia('files')) {
             return $this->getMedia('files')->map(function ($media) {
                 return [
                     'id' => $media->id,
                     'url' => $media->getUrl(),
                     'name' => $media->file_name,
                     'size' => $media->size,
                 ];
             });
         }
 
         return null;
     }
}
