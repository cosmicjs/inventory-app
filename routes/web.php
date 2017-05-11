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


Route::get('/{location?}', 'IndexController@index');
Route::get('items/{slug}', 'IndexController@itemsByLocation');
Route::post('locations/new','IndexController@newLocation');
Route::post('items/new','IndexController@newItem');
Route::post('items/edit','IndexController@editItem');
