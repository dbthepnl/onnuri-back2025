<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
                "id" => $this->id,
                "name" => $this->name,
                "phone" => $this->phone,
                "email" => $this->email,
                'intro' => $this->intro,
                'my_title' => $this->my_title,
                'my_content' => $this->my_content,
                "created_at" => $this->created_at,
                'files' => $this->getFiles(), //files 가져오기
            ];
    }
}
