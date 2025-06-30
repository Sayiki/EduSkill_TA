<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // If the Admin model directly has a 'name' or 'email' field:
            // 'name' => $this->name,
            // 'email' => $this->email,

            // If Admin has a relationship to a User model, eager load the user here
            'user' => new UserResource($this->whenLoaded('user')),
            // Add other admin-specific fields if needed
        ];
    }
}