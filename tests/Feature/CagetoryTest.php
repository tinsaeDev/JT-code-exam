<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class CagetoryTest extends TestCase
{


    /**
     * Create a new category
     */
    public function testCreateCategory(): void
    {

        $vestValues = [
            "name"=>"Tinsae"
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/categories', 
            ['name' => $vestValues["name"] ]
        );
        $response->assertStatus(201);
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('data')
                        ->has('data.name')
                        ->whereType('data.name', "string" )
                        ->where('data.name', $vestValues["name"] )


        );
    }

    /**
     * Test With Empty name responses     */
    public function testNotCreatedWithEmptyName(): void
    {

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/categories', 
            ['name' => "" ]
        );
        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('message')
                    ->has('errors')
                        ->has('errors.name')
                        ->whereType('errors.name', "array" )
                        

        );
    }


        /**
     * Test With No name responses     */
    public function testNotCreatedWithNoName(): void
    {

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/categories', 
            [  ]
        );
        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('message')
                    ->has('errors')
                        ->has('errors.name')
                        ->whereType('errors.name', "array" )
                        

        );
    }


            /**
     * Test With No name responses     */
    public function testNotCreatedWithLongName(): void
    {
        $vestValues = [
            "name"=>"This text must be long to be category name, yes"
        ];


        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/categories', 
            [ "name"=>$vestValues["name"] ]
        );
        $response->assertStatus(422);
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->has('message')
                    ->has('errors')
                        ->has('errors.name')
                        ->whereType('errors.name', "array" )
                        ->where('errors.name', fn ($nameErrors) => $nameErrors->contains("The name field must not be greater than 25 characters.") )        
        );
    }




}
