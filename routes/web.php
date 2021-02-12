<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('internals.home');
});

Route::get('/about', function () {
	return view('internals.about');
});

Route::get('/home', function () {
	return view('internals.home');
});

Route::post('/comment/{link}', 'PostController@comment');

Route::get('/{post_name}', 'PostController@post');