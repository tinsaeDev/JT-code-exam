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




    private function createCategory()
    {
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


    private function createPost()
    {
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


    /**
     * Retrieval all posts test cases
     */

    public function testRetrieveAll()
    {


        /**
         * Create categories
         */
        $categoryId = $this->createCategory();

        // Create (n) Posts
        $postCount = fake()->numberBetween(1, 5);
        for ($i = 0; $i < $postCount; $i++) {

            $postValues = [
                "title" => fake()->name(),
                "content" => fake()->text(),
                "category_id" => $categoryId,

            ];
            $imageCount = fake()->numberBetween(1, 5);
            Storage::fake("local");
            for ($j = 0; $j < $imageCount; $j++) {
                $postValues["images"][] = UploadedFile::fake()->image("sample.png");
            }

            $this->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => "application/x-www-form-urlencoded"

            ])->post(
                '/api/posts',
                $postValues
            );
        }


        // Retrieve created posts
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get(
            '/api/posts'
        );

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->missing("errors")
                ->missing("message")
                ->has("data")
                ->whereType("data", "array")
                ->where("data", fn ($data) => sizeof($data) === $postCount)
                ->hasAll(
                    [
                        "data.0.title",
                        "data.0.content",
                        "data.0.id",
                        "data.0.category_id",
                        "data.0.images",
                        "data.0.comments"
                    ]
                )

        );
        // dd($response);
    }


    public function testShowAPost()
    {

        /**
         * Create categories
         */
        $categoryId = $this->createCategory();
        $postValues = [
            "title" => fake()->name(),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];
        $imageCount = fake()->numberBetween(1, 5);
        Storage::fake("local");
        for ($j = 0; $j < $imageCount; $j++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $postCreateResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $postId = json_decode($postCreateResponse->content())->data->id;

        // Retrieve the post

        // Retrieve created posts
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->get(
            "/api/posts/$postId"
        );

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->missing("errors")
                ->missing("message")
                ->has("data")
                ->hasAll(
                    [
                        "data.title",
                        "data.content",
                        "data.id",
                        "data.category_id",
                        "data.images",
                        "data.comments"
                    ]
                )
        );
    }
}
