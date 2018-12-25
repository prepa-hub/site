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

Auth::routes();
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::redirect('/', '/' . locale()->current(), 301);
Route::prefix('{lang}')->group(function () {
    Route::redirect('/', locale()->current() . '/home', 301);
    Route::get('/home/{sort?}', 'HomeController@index')->where('sort', '[A-Za-z]+')->name('home');
    Route::get('/upload', 'FileController@addFile')->name('addFile');
    Route::post('/upload', 'FileController@handleAddFile')->name('handleAddFile');
    Route::post('/store', 'FileController@storeFile')->name('storeFile');
    Route::post('/uploadImage', 'UserController@uploadImage')->name('uploadImage');
    Route::post('/updateProfile', 'UserController@updateProfile')->name('updateProfile');
    Route::get('/settings', 'UserController@settings')->name('settings');
    Route::get('/activate/{id}', 'UserController@activate')->name('activate');
    Route::post('/search', 'HomeController@search');
    Route::get('/search', 'HomeController@searchRedirect');
    Route::get('/{uuid}/download', 'FileController@download')->name('files.download');
    Route::get('/{uuid}/view', 'FileController@view')->name('files.view');

});

