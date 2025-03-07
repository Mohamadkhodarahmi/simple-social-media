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
            'name'=> $this->name,
            'email' => $this->email,
            'follower_count' => $this->followers->count(),
            'following_count' => $this->following->count(),
            'is_followed' => auth()->check() ? auth()->user()->following()->where('users.id',$this->id)->exists() : false,

        ];
    }
}
