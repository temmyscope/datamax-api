<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\{
    Http, Request,
};
use \Seven\JsonDB\{JsonDB, Table};

class BooksController extends Controller
{
    public function fetch(Request $request)
    {
        $nameOfBook = $request->input('name');
        $collection = Http::get("https://www.anapioficeandfire.com/api/books", [
            'name' => $nameOfBook,
        ])->collect();
        
        if ($collection->isEmpty()) {
            return [
                "status_code" => 200, "status" => "success", "data" => [],
            ];
        }
        $collection->transform(function($item, $key){
            $item['number_of_pages'] = $item['numberOfPages'];
            $item['release_date'] = $item['released'];
            return $item;
        });
        //contains('key', 'value')
        return [
            'status_code' => 200, 'status' => 'success', 
            'data' => $collection->only([
                "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])->all()
        ];
    }

    public function create(Request $request, JsonDB $jsondb)
    {
        //$books = $jsondb->setTable('books');
        //$books->id = $books->generateId(Table::TYPE_INT);
        //$books->name = $request->input('name');
        //$books->isbn = $request->input('isbn');
        //$books->authors = $request->input('authors');
        //$books->country = $request->input('country');
        //$books->number_of_pages = $request->input('number_of_pages');
        //$books->publisher = $request->input('publisher');
        //$books->release_date = $request->input('release_date');
        //$books->save();
        //
        return [
            'status_code' => 201, 'status' => 'success', 
            'data' => [
                "book" => [
                    "name" => $name, "isbn" => $isbn, "authors" => $authors, 
                    "number_of_pages" => $number_of_pages, "publisher" => $publisher, 
                    "country" => $country, "release_date" => $release_date
                ]
            ]
        ];
    }

    public function getFromLocalBase(Request $request)
    {
        $condition = [];
        if( $request->has('name') ) $condition['name'] = $request->input('name');
        if( $request->has('country') ) $condition['country'] = $request->input('country');
        if( $request->has('publisher') ) $condition['publisher'] = $request->input('publisher');
        if( $request->has('release_date') ) $condition['release_date'] = $request->input('release_date');
        
        //search using any of this
        //$books = JsonDB::init('datamax', 'books');
        /*
        $books->find();
        */
        $collection = collect([]);//get from localdb
        if ($collection->isEmpty()) {
            return [
                "status_code" => 200, "status" => "success", "data" => [],
            ];
        }
        return [
            'status_code' => 200, 'status' => 'success', 
            'data' => $collection->only([
                "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])->all()
        ];
    }

    public function deleteFromBase(Request $request, $id)
    {
        $condition = ['id' => $id ];

        return [
            'status_code' => 204, 'status' => 'success', 
            "message" => "The book {$book} was updated successfully",
            "data" => [
                'id' => $id, "name" => $update['name'], "isbn" => $update['isbn'], 
                "authors" => $update['authors'], "number_of_pages" => $update['number_of_pages'], 
                "publisher" => $update['publisher'], "country" => $country, "release_date" => $release_date, 
            ]
        ];
    }

    public function getById(Request $request, $id)
    {
        $condition = ['id' => $id ];
        //book by id
        return [
            'status_code' => 200, 'status' => 'success', 
            "message" => "The book {$book} was updated successfully",
            "data" => [
                'id' => $id, "name" => $update['name'], "isbn" => $update['isbn'], 
                "authors" => $update['authors'], "number_of_pages" => $update['number_of_pages'], 
                "publisher" => $update['publisher'], "country" => $country, "release_date" => $release_date, 
            ]
        ];
    }

    public function updateById(Request $request, $id)
    {
        $update = [];
        if( $request->has('name') ) $update['name'] = $request->input('name');
        if( $request->has('isbn') ) $update['isbn'] = $request->input('isbn');
        if( $request->has('authors') ) $update['authors'] = $request->input('authors');
        if( $request->has('country') ) $update['country'] = $request->input('country');
        if( $request->has('number_of_pages') ) $update['number_of_pages'] = $request->input('number_of_pages');
        if( $request->has('publisher') ) $update['publisher'] = $request->input('publisher');
        if( $request->has('release_date') ) $update['release_date'] = $request->input('release_date');
        $condition = ['id' => $id ];

        return [
            'status_code' => 200, 'status' => 'success', 
            "message" => "The book My First Book was updated successfully",
            "data" => [
                'id' => $id, "name" => $update['name'], "isbn" => $update['isbn'], 
                "authors" => $update['authors'], "number_of_pages" => $update['number_of_pages'], 
                "publisher" => $update['publisher'], "country" => $country, "release_date" => $release_date, 
            ]
        ];
    }
}
