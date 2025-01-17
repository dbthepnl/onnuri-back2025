<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
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
        "public" => $this->public,
        "order" => $this->order,
        'title' => $this->title,
        "goal_price" => $this->goal_price,
        'current_price' => $this->current_price,
        'donation_photo' => $this->img,
        'board' => $this->board,
        'content' => $this->content,
        'created_at' => $this->created_at->toISOString(),
        ];
    }
}
