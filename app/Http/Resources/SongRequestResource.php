<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SongRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $request['id'],
            'title' => $request['title'],
            'description' => $request['description'],
            'updated_at' => $request['updated_at'],
            'avatar_id' => $request->user()->avatar_id,
            'avatar_image' => $request->user()->avatar_image,
            'name' => $request->user()->name
        ];
    }
}
