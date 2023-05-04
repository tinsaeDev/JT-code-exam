<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;


    protected $fillable = [
        "text"
    ];
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
