<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class PostTest extends TestCase
{

    public function testCreateCategory(): int
    {


        $categoryName = fake()->name();

        $response = $this->withHeaders([
            'Accept' => 'application/json'
            
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );
        $response->assertStatus(201);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->has('data.name')
                ->whereType('data.name', "string")
                ->where('data.name', $categoryName)


        );

        return json_decode($response->content())->data->id;
    }


    /**
     * @depends testCreateCategory
     */
    public function testCreatePostWithouImage($categoryId)
    {


        $postValues = [
            "title" => fake()->name(),
            "content" => fake()->text(),
            "category_id" => $categoryId
        ];

        // $this->assert( 100,199 );
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/posts',
            $postValues
        );
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                    ->missing("errors")
                ->has('data.title')
                ->whereType('data.title', "string")
                ->has('data.title')
                ->whereType('data.content', "string")

                ->has('data.category_id')
                ->where('data.category_id',$categoryId)                
                ->has('data.images')
                ->where('data.images', fn($images) => sizeof( $images )===0 )
                


        );
    }

       /**
     * @depends testCreateCategory
     */
    public function testCreatePostWithImage($categoryId)
    {



        Storage::fake("local");
        
        

        $postValues = [
            "title" => fake()->name(),
            "content" => fake()->text(),
            "category_id" => $categoryId,
            
        ];

        $imageCount = fake()->numberBetween(1,5);

        $images = [];
        for( $i=0; $i<$imageCount; $i++ ){
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        // $this->assert( 100,199 );
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type'=>"application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                    ->missing("errors")
                ->has('data.title')
                ->whereType('data.title', "string")
                ->has('data.title')
                ->whereType('data.content', "string")

                ->has('data.category_id')
                ->where('data.category_id',$categoryId)                
                ->has('data.images')
                ->where('data.images', fn($images) => sizeof( $images )===$imageCount )
                ->has('data.images.0.id' )
                ->has('data.images.0.url'  )
                ->missing('data.images.0.path' )
                ->missing('data.images.0.model' )
                ->missing('data.images.0.model_id' )
                
                


        );
    }
}
