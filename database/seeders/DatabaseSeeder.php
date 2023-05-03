<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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



    Category::factory(20)->create()
        ->each(function($category){
            Post::factory(3)->create([
                'category_id' => $category->id,              
            ])->each( function($post){
                Comment::factory(3)->create( [
                    "post_id"=>$post->id
                ] );

                Image::factory( fake()->numberBetween(0, 5) )->create( [
                    "model_id"=>$post->id,                    
                    "model"=>Post::class,
                ] );
            } );
    });



    }
}
