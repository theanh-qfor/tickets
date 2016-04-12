<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/','HomeController@index');
// Authentication routes...
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');
// Registration routes...
Route::get('register', 'Auth\AuthController@getRegister');
Route::post('register', 'Auth\AuthController@postRegister');

Route::auth();

Route::get('tickets','TicketsController@getTickets');
Route::delete('tickets','TicketsController@deleteTickets');
Route::post('tickets/assign','TicketsController@assignTicket');
Route::get('/index', function(){
    return view("index");
});
Route::get('suggest/user','SuggestController@userSuggest');
Route::post('/add_tickets', 'TicketsController@addTicket');
Route::post('/upload', 'TicketsController@upload');
Route::post('/post_comment', 'TicketsController@post_comment');
Route::post('/get_files_and_comments', 'TicketsController@get_file_and_comment');
