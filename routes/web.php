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



Route::get('/', 'HomeController@index')->name('home');

Route::get('tasks', 'TaskController@index')->name('tasks.index');
Route::post('tasks', 'TaskController@store')->name('tasks.store');
Route::put('tasks/sync', 'TaskController@sync')->name('tasks.sync');
Route::put('tasks/{id}', 'TaskController@update')->name('tasks.update');

Route::post('columns', 'ColumnController@store')->name('columns.store');
Route::delete('columns/{id}', 'ColumnController@delete')->name('columns.delete');
// Route::put('columns', 'ColumnController@update')->name('columns.update');

Route::get('dump', 'ColumnController@exportAllColumns')->name('columns.update');
