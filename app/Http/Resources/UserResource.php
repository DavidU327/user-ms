<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'identification' => $this->identification,
            'photo' => $this->image,
            'points' => $this->points,
            'rol' => [
                'id' => $this->rol->id,
                'name' => $this->rol->name,
            ],
            'state' => [
                'id' => $this->state->id,
                'name' => $this->state->name,
                'color' => $this->state->color,
            ],
        ];
    }
}
