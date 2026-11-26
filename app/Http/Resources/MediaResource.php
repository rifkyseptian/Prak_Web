<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    // Rubrik API Resource: format konsisten dan relasi memakai whenLoaded.
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'creator' => $this->creator,
            'release_year' => $this->release_year,
            'duration_minutes' => $this->duration_minutes,
            'platform' => $this->platform,
            'users' => UserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
