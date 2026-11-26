<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    // Rubrik API Resource: relasi koleksi hanya ditampilkan bila sudah dimuat.
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'media' => MediaResource::collection($this->whenLoaded('mediaCollection')),
        ];
    }
}
