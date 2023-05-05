<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;



class CagetoryTest extends TestCase
{


    use RefreshDatabase;





    /**
     * Create a new category
     */
    public function testCreateCategory(): void
    {


        $categoryName = fake()->name();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
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
    }

    /**
     * Test With Empty name responses     */
    public function testNotCreatedWithEmptyName(): void
    {

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => ""]
        );
        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('message')
                ->has('errors')
                ->has('errors.name')
                ->whereType('errors.name', "array")


        );
    }


    /**
     * Test With No name responses     */
    public function testNotCreatedWithNoName(): void
    {

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            []
        );
        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('message')
                ->has('errors')
                ->has('errors.name')
                ->whereType('errors.name', "array")


        );
    }


    /**
     * Test With No name responses     */
    public function testNotCreatedWithLongName(): void
    {



        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ["name" => fake()->text()]
        );
        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('message')
                ->has('errors')
                ->has('errors.name')
                ->whereType('errors.name', "array")
                ->where('errors.name', fn ($nameErrors) => $nameErrors->contains("The name field must not be greater than 25 characters."))
        );
    }


    /**
     * Retrieve categories
     */

    /**
     * Test all categories retrieval*/
    public function testRetrieveAllCategories(): void
    {



        $categoryName = fake()->name();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );



        // Retrieve all categories

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/categories');

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing('errors')
                ->whereType('data', "array")
                ->has('data.0.name')
                ->where('data.0.name', $categoryName)
                ->has('data.0.id')
                ->has('data.0.created_at')
                ->has('data.0.updated_at')
                ->whereType('data.0.name', "string")
        );
    }



    /**
     * Test update a category */

    public function testCategoryUpdate(): void
    {






        $categoryName = fake()->name();

        $createResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );


        $id = json_decode($createResponse->getContent())->data->id;


        // Retrieve the created  category
        // 
        $newCategoryName = fake()->name();


        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->put('/api/categories/' . $id, [
            "name" => $newCategoryName
        ]);

        // $response->dd();
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing('errors')
                ->has('data.name')
                ->whereType('data.name', "string")
                ->where('data.name',   trim($newCategoryName))
                ->has('data.id')
                ->where('data.id', $id)
                ->has('data.created_at')
                ->has('data.updated_at')
        );
    }

    /**
     * Test show singlw category retrieval*/

    public function testSingleCategoryRetrieval(): void
    {



        $categoryName = fake()->name();


        $creaeResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );


        $id = json_decode($creaeResponse->getContent())->data->id;


        // Retrieve the created  category

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/categories/' . $id);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('data')
                ->missing('errors')
                ->has('data.name')
                ->whereType('data.name', "string")
                ->where('data.name',   trim($categoryName))
                ->has('data.id')
                ->where('data.id', $id)
                ->has('data.created_at')
                ->has('data.updated_at')
        );
    }


    /**
     * Test show singlw category retrieval*/

    public function testDeleteCategory(): void
    {



        $categoryName = fake()->name();


        $creaeResponse = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post(
            '/api/categories',
            ['name' => $categoryName]
        );


        $id = json_decode($creaeResponse->getContent())->data->id;


        // Delete the created  category

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->delete('/api/categories/' . $id);

        $response->assertStatus(200);
    }
}
