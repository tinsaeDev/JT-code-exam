<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use  App\Models\Category;
use  App\Models\Post;
use  App\Models\Comment;
use  App\Models\Image;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


    $this->call([
        // CategorySeeder::class,
        // PostSeeder::class,
        // CommentSeeder::class,
        // ImageSeeder::class
    ]);



    Category::factory(3)->create()
        ->each(function($category){

            // Create posts under category
            Post::factory( fake()->numberBetween(3, 5) )->create([
                'category_id' => $category->id,              
            ])->each( function($post){

                // Create fake comments for post 
                Comment::factory(3)->create( [
                    "post_id"=>$post->id
                ] )->each( function($comment){
                    
                    // Create fake image for comments
                    Image::factory( fake()->numberBetween(0, 5) )->create( [
                        "model_id"=>$comment->id,                    
                        "model"=>Comment::class,
                    ] );
                } );

                // Create fake image records for post
                Image::factory( fake()->numberBetween(0, 5) )->create( [
                    "model_id"=>$post->id,                    
                    "model"=>Post::class,
                ] );
            } );
    });



    }
}
