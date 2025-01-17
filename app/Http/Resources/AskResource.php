<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AskResource extends JsonResource
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
        'title' => $this->title,
        "content" => $this->content,
        'password' => $this->password,
        'board' => $this->board,
        'name' => $this->name,
        'created_at' => $this->created_at->toISOString(),
        'files' => $this->getFiles(), 
        ];
    }
}
