<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\URL;

class Image extends Model
{
    use HasFactory;

    protected $appends = ['url'];


 
        /**
     * Get the user's first name.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>  URL::to( Storage::url( $this->path ) )   ,
        );
    }

   
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
