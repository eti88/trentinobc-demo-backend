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
    
    Route::group(['prefix' => 'auth'], function() {  
        Route::post('logout', 'API\Auth\LoginController@logout')->name('api.logout');
        Route::get('me', 'API\Auth\LoginController@me')->name('api.me');
    });

    Route::get('/transactions', 'API\TransactionController@index')->where([
        'type' => '[A-Za-z0-9]+',
        'from' => '[A-Za-z]+|\d{4}(?:-\d{1,2}){2}',
        'to' => '[A-Za-z]+|\d{4}(?:-\d{1,2}){2}'
    ])->name('api.txs');
    Route::post('/upload', 'API\BusinessController@uploadFile')->name('api.upload');
});

Route::fallback('API\FallbackController@index');