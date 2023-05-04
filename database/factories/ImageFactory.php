<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        // /home/sazx/Projects/Job-hunt/jacktech-code-exam/storage/app/
        
        // public/d8996839b52587c8d89c647a9151330c.png
        $absPath = fake()->image(Storage::disk('local')->path("public"), 200, 200); 
        $storagePath =   Storage::path("/");
        return [
            "path"=> Str::replace( $storagePath , '',  $absPath )
            
        ];
    }
}
