<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function() {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/profile', 'AuthController@profile');

    Route::group(['prefix' => 'tasks'], function () {
        Route::get('', 'TaskController@index');
        Route::post('', 'TaskController@store');
        Route::get('{task}', 'TaskController@show')->where('task', '[0-9]+');
        Route::patch('{task}', 'TaskController@update')->where('task', '[0-9]+');
        Route::delete('{task}', 'TaskController@destroy')->where('task', '[0-9]+');
        Route::patch('{task}/completion-status', 'TaskController@updateCompletionStatus')->where('task', '[0-9]+');
    });
});
