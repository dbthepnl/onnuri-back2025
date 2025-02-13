<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            'username' => $this->username,
            'phone' => $this->phone,
            'role' => $this->role,
            'gender' => $this->gender,
            'name' => $this->name,
            'birth' => $this->birth,
            'birth_type' => $this->birth_type,
            'address' => $this->address,
            'detail_address' => $this->detail_address,
            'zip_code' => $this->zip_code,
            'email' => $this->email,
            'homepage' => $this->homepage,
            'officers' => $this->officers,
            'my_profile_photo' => $this->getMedia('my_profile_photo')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->file_name,
                    'size' => $media->size,
                ];
            }),
        ];
    }
}
