<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
| API witouth Token required
*/
Route::group(['prefix' => 'v1', 'middleware' => 'guest:api'], function () {

    Route::group(['prefix' => 'auth'], function() { 
        Route::post('login', 'API\Auth\LoginController@login')->name('api.login');
    });
});

/*
| API with Token required
*/
Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::post('/upload', 'API\BusinessController@uploadFile')->name('api.upload');
});

Route::fallback('API\FallbackController@index');