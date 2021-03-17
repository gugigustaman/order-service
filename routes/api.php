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

	Route::get('/product', [
		'as' => 'product.list',
		'uses' => 'ProductController@list'
	]);

	Route::group(['prefix' => 'order'], function() {
		Route::get('/', [
			'as' => 'order.list',
			'uses' => 'OrderController@list'
		]);
	});

	Route::group(['prefix' => 'cart'], function() {
		Route::get('/', [
			'as' => 'cart.detail',
			'uses' => 'CartController@detail'
		]);

		Route::post('/add_item', [
			'as' => 'cart.add_item',
			'uses' => 'CartController@addItem'
		]);

		Route::post('/remove_item', [
			'as' => 'cart.remove_item',
			'uses' => 'CartController@removeItem'
		]);

		Route::post('/pay', [
			'as' => 'cart.pay',
			'uses' => 'CartController@pay'
		]);
	});
});