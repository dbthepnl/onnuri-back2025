<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrainCollection extends ResourceCollection
{
    public $board; 

    public function __construct($resource, $board)
    {
        parent::__construct($resource);
        $this->board = $board; 
    }

    public function toArray($request)
    {
        return [
            'data' => $this->collection, 
            'board' => $this->board, 
        ];
    }
}
