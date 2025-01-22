<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PopupCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Check if the collection is paginated (i.e., LengthAwarePaginator)
        $data = $this->collection->transform(function ($post) {
            // Add the 'image_url' field (using the 'getImgAttribute' method)
            $post->notice_photo = $post->img;  // This will call the 'getImgAttribute()' method

            // Optionally, remove the media field if it's included by default
            unset($post->media); // Remove the media array from the response

            return $post;
        });

        // Return the paginated data as an array
        return [
            'data' => $data,
        ];
    }
}
