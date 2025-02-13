<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
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
        "urls" => $this->urls,
        "form_id" => $this->form_id,
        "form_check" => $this->form_check,
        "board_id" => $this->board_id,
        "cardinal_id" => $this->cardinal_id,
        "cardinal_check" => $this->cardinal_check,
        "order" => $this->order,
        "update_on" => $this->update_on,
        "public" => $this->public,
        "board" => $this->board,
        "category" => $this->category,
        "category_id" => $this->category_id,
        'title' => $this->title,
        'content' => $this->content,
        'bus_content' => $this->bus_content,
        'safe_content' => $this->safe_content,
        'notice_photo' => $this->img,
        'start_at' => $this->start_at,
        'end_at' => $this->end_at,
        'time_at' => $this->time_at,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'files' => $this->getMedia('files')->map(function ($media) {
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
