<?php

/** 
* @var \Laravel\Lumen\Routing\Router $router
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api'],  function() use ($router){
    
    $router->get('/external-books', 'BooksController@fetch');

    $router->get('/v1/books', 'BooksController@getFromLocalBase');

    $router->post('/v1/books', 'BooksController@create');

    $router->get('/v1/books/{id}', 'BooksController@getById');

    $router->patch('/v1/books/{id}', 'BooksController@updateById');

    $router->delete('/v1/books/{id}', 'BooksController@deleteFromBase');

});