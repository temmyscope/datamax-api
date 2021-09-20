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
        $jsondb = JsonDB::init(
            directory: __DIR__.'/../../../storage', database: 'datamax'
        );
        return $next($request, $jsondb);
    }
}
