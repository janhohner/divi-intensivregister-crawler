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

/*Route::get('/',                         'StaticPageController@home')        ->name('home');
Route::get('impressum',                 'StaticPageController@impressum')   ->name('impressum');

Route::get('data/load/clinics',         'DiviDataController@showAllLoad')   ->name('data.load.clinics');
Route::get('data/load/clinic/{id}',     'DiviDataController@showLoad')      ->name('data.load.clinic');*/
Route::get('data/load/export/{type}',   'DiviDataController@exportLoad')    ->name('data.load.export');

/*Route::get('data/cases/clinics',        'DiviDataController@showAllCases')   ->name('data.cases.clinics');
Route::get('data/cases/clinic/{id}',    'DiviDataController@showCases')      ->name('data.cases.clinic');*/
Route::get('data/cases/export/{type}',  'DiviDataController@exportCases')    ->name('data.cases.export');

Route::get('data/divi/export/{type}',   'DiviDataController@exportDivi')     ->name('data.divi.export');

/* backwards compatibility */
/*Route::get('clinics', function () { return redirect()->route('data.load.clinics'); });
Route::get('clinic/{id}', function ($id) { return redirect()->route('data.load.clinic', ['id' => $id]); });*/
Route::get('export/{type}', function ($type) { return redirect()->route('data.load.export', ['type' => $type]); });
