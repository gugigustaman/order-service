<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

Route::group([
	'prefix' => 'api'
], function() {
	Route::post('/login', [
		'as' => 'auth.login',
		'uses' => 'AuthController@login'
	]);

	Route::get('/products', [
		'as' => 'product.list',
		'uses' => 'ProductController@list'
	]);
});