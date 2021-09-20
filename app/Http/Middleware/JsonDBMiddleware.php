<?php

namespace App\Http\Middleware;

use Closure;

use Seven\JsonDB\JsonDB;

class JsonDBMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        JsonDB::make(
            directory: __DIR__.'/../../../storage', database: 'datamax'
        );
        $jsondb = JsonDB::init(
            directory: __DIR__.'/../../../storage', database: 'datamax'
        );
        $jsondb->createTable('books', [
            "name", "isbn", "authors", "number_of_pages", "publisher", "country", "release_date"
        ]);
        return $next($request, $jsondb);
    }
}
