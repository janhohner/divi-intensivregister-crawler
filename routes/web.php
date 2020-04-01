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

Route::get('/',                         'StaticPageController@home')        ->name('home');
Route::get('impressum',                 'StaticPageController@impressum')   ->name('impressum');

Route::get('data/load/clinics',         'DiviDataController@showAllLoad')   ->name('data.load.clinics');
Route::get('data/load/clinic/{id}',     'DiviDataController@showLoad')      ->name('data.load.clinic');
Route::get('data/load/export/{type}',   'DiviDataController@exportLoad')    ->name('data.load.export');

/* backwards compatibility */
Route::get('clinics', function () { return redirect()->route('data.load.clinics'); });
Route::get('clinic/{id}', function ($id) { return redirect()->route('data.load.clinic', ['id' => $id]); });
Route::get('export/{type}', function ($type) { return redirect()->route('data.load.export', ['type' => $type]); });
