<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use \Seven\JsonDB\{ JsonDB, Table };
use \Seven\Vars\Arrays;

class BooksController extends Controller
{
    public function __construct()
    {
        $this->jsondb = JsonDB::init(
            directory: __DIR__.'/../../../storage', database: 'datamax'
        );
    }
    public function getTenBooksFromApi(Request $request)
    {
        $result = curl("https://www.anapioficeandfire.com/api/books")->setMethod('GET')->send();
        (array)$decodedData = json_decode($result, true);
        $collection = Arrays::init($decodedData);
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
            'data' => Arrays::init($collection->trim(10, 0))->whitelist([
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

    public function create(Request $request)
    {
        $books = $this->jsondb->setTable('books');
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
                    "name" => $books->name, "isbn" => $books->isbn, 
                    "authors" => $books->authors, "number_of_pages" => $books->number_of_pages, 
                    "publisher" => $books->publisher, 
                    "country" => $books->country, "release_date" => $books->release_date
                ]
            ]
        ]);
    }

    public function getFromLocalBase(Request $request)
    {
        $condition = [];
        if( $request->has('name') ) $condition['name'] = $request->input('name');
        if( $request->has('country') ) $condition['country'] = $request->input('country');
        if( $request->has('publisher') ) $condition['publisher'] = $request->input('publisher');
        if( $request->has('release_date') ) $condition['release_date'] = $request->input('year');
        
        $books = $this->jsondb->setTable('books');
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
            ])
        ]);
    }

    public function deleteFromBase(Request $request, $id)
    {
        $condition = ['id' => (int)$id ];
        $books = $this->jsondb->setTable('books');
        $book = $books->findById((int)$id);
        $books->delete($condition);
        $deleted= $book['name'] ?? $book->name ?? 'unknown';
        
        return response()->json([
            'status_code' => 204, 'status' => 'success', 
            "message" => "The book {$deleted} was updated successfully",
            "data" => []
        ]);
    }

    public function getById(Request $request, $id)
    {
        $books = $this->jsondb->setTable('books');
        $book = $books->findById((int)$id);
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
            "data" => $collection->whiteList([
                "id", "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
            ])[0]
        ]);
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
        $books = $this->jsondb->setTable('books');
        $books->update($update, ['id' => $id]);

        return response()->json([
            'status_code' => 200, 'status' => 'success', 
            "message" => "The book My First Book was updated successfully",
            "data" => [
                'id' => $id, "name" => $update['name'], "isbn" => $update['isbn'], 
                "authors" => $update['authors'], "number_of_pages" => $update['number_of_pages'], 
                "publisher" => $update['publisher'], "country" => $update['country'], 
                "release_date" => $update['release_date'], 
            ]
        ]);
    }
}
