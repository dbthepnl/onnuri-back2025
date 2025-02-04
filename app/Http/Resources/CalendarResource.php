<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarResource extends JsonResource
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
        "board" => $this->board,
        "category_id" => $this->category_id,
        "name_ko" => $this->category_id,
        "room_name" => $this->room_name, //방 이름
        "colorcode" => $this->category_id,
        "location_id" => $this->location_id,
        "category" => $this->category,
        'title' => $this->title,
        'content' => $this->content,
        'start' => $this->start,
        'end' => $this->end,
        'background_color' => $this->background_color,
        'border_color' => $this->border_color,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        ];
    }
}
