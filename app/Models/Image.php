<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;


   
    /**
     * Get the category that owns the post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

        /**
     * Get the category that owns the post.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
