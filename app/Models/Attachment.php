<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = ['post_id','file_path','file_type'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
