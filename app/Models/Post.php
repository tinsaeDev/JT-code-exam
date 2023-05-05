<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;


    
    protected $fillable = [
        "title","content","category_id"
    ];
         /**
     * Get the comments of  post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }


     /**
     * Get the images of  post.
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'model_id')->where('model', Post::class );

    }

    
    /**
     * Get the category that owns the post.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
