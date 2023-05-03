<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;


             /**
     * Get the posts for the blog category.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

        /**
     * Get the category that owns the post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }


}
