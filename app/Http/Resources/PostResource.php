<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'content' => $this->content,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'likes_count' => $this->comments->count(),
            'attachments' => $this->attachments->map(fn($attachment) =>[
                'id' => $attachment->id,
                'file_path' => $attachment->file_path,
                'file_type' => $attachment->file_type
            ]),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
