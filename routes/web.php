<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/',             'DiviDataController@showAll')   ->name('clinics');
Route::get('clinic/{id}',   'DiviDataController@show')      ->name('clinic');

Route::get('export/{type}', 'DiviDataController@export')    ->name('export');
