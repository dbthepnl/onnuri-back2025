<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "urls" => $this->urls,
            "public" => $this->public,
            "board" => $this->board,
            'title' => $this->title,
            'notice_photo' => $this->img,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
            ];
    }
}
