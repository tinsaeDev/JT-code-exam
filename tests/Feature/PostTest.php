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


    use RefreshDatabase;




    private function createCategory(){
        $categoryName = fake()->name();

        $categoryResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );

        $categoryId = json_decode($categoryResponse->content())->data->id;
        return $categoryId;
    }


    public function testCreatePostWithouImage()
    {



  
        $categoryId = $this->createCategory();



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
                ->where('data.category_id', $categoryId)
                ->has('data.images')
                ->where('data.images', fn ($images) => sizeof($images) === 0)



        );
    }

    /**
     * Create a new post with images
     *
     */
    public function testCreatePostWithImage()
    {




  
        $categoryId = $this->createCategory();

        Storage::fake("local");



        $postValues = [
            "title" => fake()->name(),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = fake()->numberBetween(1, 5);

        $images = [];
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        // $this->assert( 100,199 );
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

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
                ->where('data.category_id', $categoryId)
                ->has('data.images')
                ->where('data.images', fn ($images) => sizeof($images) === $imageCount)
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')




        );
    }


    /**
     * Test a new post without content
     */
    public function testFailsWithoutContent()
    {



  
        $categoryId = $this->createCategory();


        Storage::fake("local");



        $postValues = [
            "title" => fake()->name(),
            // "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = fake()->numberBetween(1, 5);

        $images = [];
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        // $this->assert( 100,199 );
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('errors')
                ->has("message")
                ->missing("data")

                ->missing("errors.title")
                ->missing("errors.category_id")
                ->has("errors.content")

                ->whereType("errors.content", "array")
                ->where("errors.content", fn ($errors) => $errors->contains("The content field is required."))





        );
    }

    /**
     * Test a new post without title
     */
    public function testFailsWithoutTitle()
    {




  
        $categoryId = $this->createCategory();

        Storage::fake("local");



        $postValues = [
            // "title" => fake()->name(),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = fake()->numberBetween(1, 5);


        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        // $this->assert( 100,199 );
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('errors')
                ->has("message")
                ->missing("data")

                ->has("errors.title")
                ->missing("errors.category_id")
                ->missing("errors.content")

                ->whereType("errors.title", "array")
                ->where("errors.title", fn ($errors) => $errors->contains("The title field is required."))





        );
    }

    /**
     * Test a new post without title
     */
    public function testFailsWithoutCategoryId()
    {




  
        $categoryId = $this->createCategory();

        Storage::fake("local");



        $postValues = [
            "title" => fake()->name(),
            "content" => fake()->text(),
            // "category_id" => $categoryId,

        ];

        $imageCount = fake()->numberBetween(1, 5);


        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        // $this->assert( 100,199 );
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('errors')
                ->has("message")
                ->missing("data")

                ->has("errors.category_id")
                ->missing("errors.title")
                ->missing("errors.content")

                ->whereType("errors.category_id", "array")
                ->where("errors.category_id", fn ($errors) => $errors->contains("The category id field is required."))





        );
    }
}
