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

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');

Route::middleware ('admin')->group (function () {

    Route::resource ('category', 'CategoryController', [
        'except' => 'show'
    ]);
    Route::name ('orphans.')->prefix('orphans')->group(function () {
        Route::name ('index')->get ('/', 'AdminController@orphans');
        Route::name ('destroy')->delete ('/', 'AdminController@destroy');
    });
    Route::name ('maintenance.')->prefix('maintenance')->group(function () {
        Route::name ('index')->get ('/', 'AdminController@edit');
        Route::name ('update')->put ('/', 'AdminController@update');
    });
    Route::resource ('user', 'UserController', [
        'only' => ['index', 'edit', 'update', 'destroy']
    ]);
});

Route::middleware ('auth', 'verified')->group (function () {
    Route::resource ('image', 'ImageController', [
        'only' => ['create', 'store', 'destroy', 'update']
    ]);
    Route::name ('image.')->middleware ('ajax')->group (function () {
        Route::prefix('image')->group(function () {
            Route::name ('description')->put ('{image}/description', 'ImageController@descriptionUpdate');
            Route::name ('adult')->put ('{image}/adult', 'ImageController@adultUpdate');
            Route::name ('albums')->get('{image}/albums','ImageController@albums');
            Route::name ('albums.update')->put ('{image}/albums', 'ImageController@albumsUpdate');
        });
        Route::name ('rating')->put ('rating/{image}', 'ImageController@rate');
    });
    Route::resource ('profile', 'ProfileController', [
        'only' => ['edit', 'update', 'destroy', 'show' ],
        'parameters' => ['profile' => 'user']
    ]);
    Route::resource ('album', 'AlbumController', [
        'except' => 'show'
    ]);
    Route::name ('notification.')->prefix('notification')->group(function () {
        Route::name ('index')->get ('/', 'NotificationController@index');
        Route::name ('update')->patch ('{notification}', 'NotificationController@update');
    });
});

Route::middleware('ajax')->name('image.click')->patch('image/{image}/click', 'ImageController@click');
Route::name ('category')->get ('category/{slug}', 'ImageController@category');

Route::name ('user')->get ('user/{user}', 'ImageController@user');

Route::name ('album')->get ('album/{slug}', 'ImageController@album');
Route::name('language')->get('language/{lang}', 'HomeController@language');