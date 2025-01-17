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

class Calendar extends Authenticatable implements HasMedia
{
     /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;

     /**
      * The attributes that are mass assignable.
      *
      * @var list<string>
      */
     protected $fillable = [
            'title',
            'board',
            'category',
            'content',
            'user_id',
            'start',
            'end',
            'background_color',
            'border_color',
            'created_at',
            'updated_at',
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
         if ($this->hasMedia('n_photo')) {
             $img = $this->getMedia('n_photo')->last();
             return $img->getUrl();
         }
 
         return null;
     }
}
