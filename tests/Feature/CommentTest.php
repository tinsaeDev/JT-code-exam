<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentTest extends TestCase
{

    use RefreshDatabase;


    private function createCategory()
    {
        $categoryName = fake()->name(24);

        $categoryResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );

        $categoryId = json_decode($categoryResponse->content())->data->id;
        return $categoryId;
    }
    /**
     * Create a new post with comments and comment images
     *
     */
    public function testCreateWithCommentImage()
    {


        $categoryId = $this->createCategory();
        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = fake()->numberBetween(1, 5);
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;

        // Add Comments
        $commentImageCount = fake()->numberBetween(1, 4);
        $commentValue = [
            "text" => fake()->text(),
            "images" => []
        ];

        for ($i = 0; $i < $commentImageCount; $i++) {
            $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
        }

        $addCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            "/api/posts/$postId/comments",
            $commentValue
        );

        $addCommnetResponse->assertStatus(200);

        $addCommnetResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->has('data.id')
                ->has('data.post_id')
                ->where('data.post_id', $postId)
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', function ($images) use ($commentImageCount) {
                    return  sizeof($images) === $commentImageCount;
                })
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')
                ->whereType('data.id', "integer")

        );
    }

    /**
     * Create a new post with comments and no  comment images
     *
     */
    public function testCreateWithoutCommentImage()
    {


        $categoryId = $this->createCategory();
        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = 0;
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;

        // Add Comments
        $commentImageCount = fake()->numberBetween(1, 4);
        $commentValue = [
            "text" => fake()->text(),
            "images" => []
        ];

        for ($i = 0; $i < $commentImageCount; $i++) {
            $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
        }

        $addCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            "/api/posts/$postId/comments",
            $commentValue
        );

        $addCommnetResponse->assertStatus(200);

        $addCommnetResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->has('data.id')
                ->has('data.post_id')
                ->where('data.post_id', $postId)
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', function ($images) use ($commentImageCount) {
                    return  sizeof($images) === $commentImageCount;
                })
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')
                ->whereType('data.id', "integer")

        );
    }

    /**
     * Create a new post with comments and no  comment images
     *
     */
    public function testCreateFaiWithoutCommentText()
    {


        $categoryId = $this->createCategory();
        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = 0;
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;

        // Add Comments
        $commentImageCount = fake()->numberBetween(1, 4);
        $commentValue = [
            // "text" => fake()->text(),
            "images" => []
        ];

        for ($i = 0; $i < $commentImageCount; $i++) {
            $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
        }

        $addCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            "/api/posts/$postId/comments",
            $commentValue
        );

        $addCommnetResponse->assertStatus(422);

        $addCommnetResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('errors')
                ->has("message")
                ->missing("data")
                ->whereType('errors', "array")
                ->whereType('errors.text', "array")
                ->where('errors.text', fn ($textErrors) => $textErrors[0] === "The text field is required.")

        );
    }


    /**
     * Create  retrive a post comments
     */
    public function testRetrivePostComments()
    {

        $categoryId = $this->createCategory();



        /**
         * Create post
         */

        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = 0;
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;


        /**
         * Create comments
         */
        $commentCount =  fake()->numberBetween(1, 6);
        for ($iCommentCount = 0; $iCommentCount <  $commentCount; $iCommentCount++) {

            $commentImageCount = fake()->numberBetween(1, 4);
            $commentValue = [
                "text" => fake()->text(),
                "images" => []
            ];

            for ($i = 0; $i < $commentImageCount; $i++) {
                $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
            }


            $addCommnetResponse = $this->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => "application/x-www-form-urlencoded"

            ])->post(
                "/api/posts/$postId/comments",
                $commentValue
            );

            $addCommnetResponse->assertStatus(200);

            $addCommnetResponse->assertJson(
                fn (AssertableJson $json) =>
                $json->has('data')
                    ->missing("errors")
                    ->whereType('data', "array")
                    ->has('data.id')
                    ->has('data.post_id')
                    ->where('data.post_id', $postId)
                    ->where('data.post_id', $postId)
                    ->whereType('data.images', "array")
                    ->where('data.images', function ($images) use ($commentImageCount) {
                        return  sizeof($images) === $commentImageCount;
                    })
                    ->has('data.images.0.id')
                    ->has('data.images.0.url')
                    ->missing('data.images.0.path')
                    ->missing('data.images.0.model')
                    ->missing('data.images.0.model_id')
                    ->whereType('data.id', "integer")

            );
        }


        /**
         * Retrive comments
         */

        $commentResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get(
            "/api/posts/$postId/comments"
        );

        $commentResponse->assertStatus(200);
        $commentResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->where('data',  fn ($data) => sizeof($data) === $commentCount)
                ->has('data.0.text')
                ->has('data.0.post_id')
                ->has('data.0.id')
                ->has('data.0.images')
            // TODO: Update              
        );
    }

    /**
     * Create  retrive a post comments
     */
    public function testShowComment()
    {

        $categoryId = $this->createCategory();



        /**
         * Create post
         */

        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = 0;
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;


        /**
         * Create comments
         */

        $commentImageCount = fake()->numberBetween(1, 4);
        $commentValue = [
            "text" => fake()->text(),
            "images" => []
        ];

        for ($i = 0; $i < $commentImageCount; $i++) {
            $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
        }


        $addCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            "/api/posts/$postId/comments",
            $commentValue
        );

        $addCommnetResponse->assertStatus(200);

        $addCommnetResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->has('data.id')
                ->has('data.post_id')
                ->where('data.post_id', $postId)
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', function ($images) use ($commentImageCount) {
                    return  sizeof($images) === $commentImageCount;
                })
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')
                ->whereType('data.id', "integer")

        );

        $commentId = json_decode($addCommnetResponse->content())->data->id;

        /**
         * Retrive comment
         */

        $commentResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get(
            "/api/posts/$postId/comments/$commentId"
        );

        $commentResponse->assertStatus(200);
        $commentResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->has('data.text')
                ->has('data.post_id')
                ->has('data.id')
                ->has('data.images')

                ->where('data.text', $commentValue["text"])
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', fn ($images) => sizeof($images) === $commentImageCount)
            // TODO: Update              
        );
    }


    /**
     * Create  retrive a post comments
     */
    public function testUpdateComment()
    {

        $categoryId = $this->createCategory();



        /**
         * Create post
         */

        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = 0;
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;


        /**
         * Create comments
         */

        $commentImageCount = fake()->numberBetween(1, 4);
        $commentValue = [
            "text" => fake()->text(),
            "images" => []
        ];

        for ($i = 0; $i < $commentImageCount; $i++) {
            $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
        }


        $addCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            "/api/posts/$postId/comments",
            $commentValue
        );

        $addCommnetResponse->assertStatus(200);

        $addCommnetResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->has('data.id')
                ->has('data.post_id')
                ->where('data.post_id', $postId)
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', function ($images) use ($commentImageCount) {
                    return  sizeof($images) === $commentImageCount;
                })
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')
                ->whereType('data.id', "integer")

        );

        $commentId = json_decode($addCommnetResponse->content())->data->id;

        /**
         * Retrive comment
         */

        $commentResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get(
            "/api/posts/$postId/comments/$commentId"
        );

        $commentResponse->assertStatus(200);
        $commentResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->has('data.text')
                ->has('data.post_id')
                ->has('data.id')
                ->has('data.images')

                ->where('data.text', $commentValue["text"])
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', fn ($images) => sizeof($images) === $commentImageCount)
            // TODO: Update              
        );


        /**
         * Depete Comment
         */


        $updateCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json'

        ])->delete(
            "/api/posts/$postId/comments/$commentId"
        );

        $updateCommnetResponse->assertStatus(200);


        /**
         * Show the updated comment
         */

        $response = $this->withHeaders([
            'Accept' => 'application/json',

        ])->get(
            "/api/posts/$postId/comments/$commentId"
        );



        $response->assertStatus(404);
    }


    /**
     * Create  retrive a post comments
     */
    public function testDeleteComment()
    {

        $categoryId = $this->createCategory();



        /**
         * Create post
         */

        $postValues = [
            "title" => fake()->name(24),
            "content" => fake()->text(),
            "category_id" => $categoryId,

        ];

        $imageCount = 0;
        for ($i = 0; $i < $imageCount; $i++) {
            $postValues["images"][] = UploadedFile::fake()->image("sample.png");
        }

        $createPostResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            '/api/posts',
            $postValues
        );

        $createPostResponse->assertStatus(200);
        $createPostResponse->assertJson(
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

        $postId = json_decode($createPostResponse->content())->data->id;


        /**
         * Create comments
         */

        $commentImageCount = fake()->numberBetween(1, 4);
        $commentValue = [
            "text" => fake()->text(),
            "images" => []
        ];

        for ($i = 0; $i < $commentImageCount; $i++) {
            $commentValue["images"][] =  UploadedFile::fake()->image("sample.png");
        }


        $addCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => "application/x-www-form-urlencoded"

        ])->post(
            "/api/posts/$postId/comments",
            $commentValue
        );

        $addCommnetResponse->assertStatus(200);

        $addCommnetResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->has('data.id')
                ->has('data.post_id')
                ->where('data.post_id', $postId)
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', function ($images) use ($commentImageCount) {
                    return  sizeof($images) === $commentImageCount;
                })
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')
                ->whereType('data.id', "integer")

        );

        $commentId = json_decode($addCommnetResponse->content())->data->id;

        /**
         * Retrive comment
         */

        $commentResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get(
            "/api/posts/$postId/comments/$commentId"
        );

        $commentResponse->assertStatus(200);
        $commentResponse->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->has('data.text')
                ->has('data.post_id')
                ->has('data.id')
                ->has('data.images')

                ->where('data.text', $commentValue["text"])
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', fn ($images) => sizeof($images) === $commentImageCount)
            // TODO: Update              
        );


        /**
         * Update Comment
         */

        $newCommentValues = [
            "text" => "This is updated comment text"
        ];
        $updateCommnetResponse = $this->withHeaders([
            'Accept' => 'application/json'

        ])->put(
            "/api/posts/$postId/comments/$commentId",
            $newCommentValues
        );

        $updateCommnetResponse->assertStatus(200);


        /**
         * Show the updated comment
         */

        $response = $this->withHeaders([
            'Accept' => 'application/json',

        ])->get(
            "/api/posts/$postId/comments/$commentId"
        );



        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing("errors")
                ->whereType('data', "array")
                ->has('data.id')
                ->has('data.post_id')
                ->where('data.post_id', $postId)
                ->where('data.text', $newCommentValues["text"])
                ->where('data.post_id', $postId)
                ->whereType('data.images', "array")
                ->where('data.images', function ($images) use ($commentImageCount) {
                    return  sizeof($images) === $commentImageCount;
                })
                ->has('data.images.0.id')
                ->has('data.images.0.url')
                ->missing('data.images.0.path')
                ->missing('data.images.0.model')
                ->missing('data.images.0.model_id')
                ->whereType('data.id', "integer")

        );
    }
}
