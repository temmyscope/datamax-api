<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class RoutesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoutesResponse()
    {
        $this->json('GET', '/api')->seeJson([ 'status_code' => 200 ])
        ->seeJsonStructure([
            'data' => [
                '*' => ["name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"]
            ]
        ]);

        $this->json('GET', '/api/external-books?name=A+game+of+thrones')
        ->seeJson([ 'status_code' => 200 ])
        ->seeJsonStructure([
            'data' => [
                '*' => ["name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"]
            ],
        ]);

        $this->json('POST', '/api/v1/books', [
            "name"=> "My First Book",
            "isbn" => "123-3213243567",
            "authors" => ["John Doe"],
            "country" => "United States",
            "number_of_pages" => 350,
            "publisher" => "Acme Books",
            "release_date" => "2019-08-01"
        ])->seeJson([ 'status_code' => 201, 'status' => 'success' ])
        ->seeJsonStructure([
            'data' => [
                '*' => ["name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"]
            ],
        ]);

        $this->json('GET', '/api/v1/books')->seeJson([ 'status_code' => 200])
        ->seeJsonStructure([
            'data' => [
                '*' => ["name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"]
            ],
        ]);

        $this->json('GET', '/api/v1/books?name=My+First+Book')->seeJson([ 'status_code' => 200])
        ->seeJsonStructure([
            'data' => [
                '*' => ["name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"]
            ],
        ]);

        $this->json('GET', '/api/v1/books/1')->seeJson([ 'status_code' => 200, 'status' => 'success'])
        ->seeJsonStructure([
            'data' => [
                "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ],
        ]);
        
        $this->json('PATCH', '/api/v1/books/1', [
            "name"=> "My First Book: Revised",
            "isbn" => "123-3213243567",
            "authors" => ["John Doe"],
            "country" => "United States",
            "number_of_pages" => 350,
            "publisher" => "Acme Books",
            "release_date" => "2019-08-01"
        ])->seeJson([ 
            'status_code' => 200
        ])->seeJsonStructure(["message"]);

        $this->json("DELETE", '/api/v1/books/1')->seeJson(['status_code' => 204]);
    }
}
