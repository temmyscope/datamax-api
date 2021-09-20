<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use \Seven\JsonDB\{JsonDB, Table};
use \Seven\Vars\Arrays;

class BooksController extends Controller
{

    public function getTenBooksFromApi(Request $request)
    {
        $result = curl("https://www.anapioficeandfire.com/api/books")->setMethod('GET')->send();
        $decodedData = json_decode($result, true);
        $collection = Arrays::init($result);
        if ($collection->isEmpty()) {
            return response()->json([
                "status_code" => 200, "status" => "success", "data" => [],
            ]);
        }
        $collection->apply(function($item){
            $item['number_of_pages'] = $item['numberOfPages'];
            $item['release_date'] = $item['released'];
            return $item;
        });
        return response()->json([
            'status_code' => 200, 'status' => 'success',
            'data' => $collection->trim(10, 0)->whitelist([
                "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])
        ]);
    }

    public function fetch(Request $request)
    {
        $nameOfBook = $request->query('name');
        $collection = Arrays::safeInit(
            Http::get("https://www.anapioficeandfire.com/api/books", [
                'name' => $nameOfBook,
            ])->json()
        );
        if ($collection->isEmpty()) {
            return response()->json([
                "status_code" => 200, "status" => "success", "data" => [],
            ]);
        }
        $collection->apply(function($item){
            $item['number_of_pages'] = $item['numberOfPages'];
            $item['release_date'] = $item['released'];
            return $item;
        });
        return response()->json([
            'status_code' => 200, 'status' => 'success', 
            'data' => $collection->whiteList([
                "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])
        ]);
    }

    public function create(Request $request, JsonDB $jsondb)
    {
        $books = $jsondb->setTable('books');
        $books->id = $books->generateId(Table::TYPE_INT);
        $books->name = $request->input('name');
        $books->isbn = $request->input('isbn');
        $books->authors = $request->input('authors');
        $books->country = $request->input('country');
        $books->number_of_pages = $request->input('number_of_pages');
        $books->publisher = $request->input('publisher');
        $books->release_date = $request->input('release_date');
        $books->save();
        
        return response()->json([
            'status_code' => 201, 'status' => 'success', 
            'data' => [
                "book" => [
                    "name" => $name, "isbn" => $isbn, "authors" => $authors, 
                    "number_of_pages" => $number_of_pages, "publisher" => $publisher, 
                    "country" => $country, "release_date" => $release_date
                ]
            ]
        ]);
    }

    public function getFromLocalBase(Request $request, JsonDB $jsondb)
    {
        $condition = [];
        if( $request->has('name') ) $condition['name'] = $request->input('name');
        if( $request->has('country') ) $condition['country'] = $request->input('country');
        if( $request->has('publisher') ) $condition['publisher'] = $request->input('publisher');
        if( $request->has('release_date') ) $condition['release_date'] = $request->input('year');
        
        $books = $jsondb->setTable('books');
        $booksArray = $books->search($condition, $sortBy='id');
        $collection = Arrays::safeInit($booksArray);
        if ($collection->isEmpty()) {
            return response()->json([
                "status_code" => 200, "status" => "success", "data" => [],
            ]);
        }
        return response()->json([
            'status_code' => 200, 'status' => 'success', 
            'data' => $collection->whiteList([
                "id", "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])->toArray()
        ]);
    }

    public function deleteFromBase(Request $request, JsonDB $jsondb, $id)
    {
        $condition = ['id' => $id ];
        $books = $jsondb->setTable('books');
        $book = $books->findById($id);
        $books->delete($condition);

        return response()->json([
            'status_code' => 204, 'status' => 'success', 
            "message" => "The book {$book['name']} was updated successfully",
            "data" => []
        ]);
    }

    public function getById(Request $request, JsonDB $jsondb, $id)
    {
        $books = $jsondb->setTable('books');
        $book = $books->findById($id);
        if (empty($book)) {
            return response()->json([
                'status_code' => 200, 'status' => 'success', 
                "message" => "Book with id: {$id} not found",
                "data" => []
            ]);
        }
        $collection = Arrays::safeInit([$book]);
        return response()->json([
            'status_code' => 200, 'status' => 'success', 
            "message" => "The book {$book->name} was updated successfully",
            "data" => $collection->whiteList([
                "id", "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])[0]
        ]);
    }

    public function updateById(Request $request, JsonDB $jsondb, $id)
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
        $books = $jsondb->setTable('books');
        $books->update($update, ['id' => $id]);

        return response()->json([
            'status_code' => 200, 'status' => 'success', 
            "message" => "The book My First Book was updated successfully",
            "data" => [
                'id' => $id, "name" => $update['name'], "isbn" => $update['isbn'], 
                "authors" => $update['authors'], "number_of_pages" => $update['number_of_pages'], 
                "publisher" => $update['publisher'], "country" => $country, "release_date" => $release_date, 
            ]
        ]);
    }
}
