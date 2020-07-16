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

Route::post('/register','Api\AuthController@register');
Route::post('/login','Api\AuthController@login');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout','Api\AuthController@logout');
    Route::get('/user','Api\AuthController@getUser');
    Route::post('/update-user','Api\AuthController@updateUser');
    Route::post('/update-member','Api\AuthController@update');
    Route::post('/change-password','Api\AuthController@changePassword');
    Route::post('/delete-member','Api\AuthController@destroy');
    Route::get('/admin/users','Api\AuthController@getUsers');
});

Route::post('/password/forgot','Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset','Api\ResetPasswordController@reset');


Route::get('admin/notice/index', 'NoticeController@index');
Route::prefix('admin/notice')->middleware('auth:api')->group(function (){
    Route::post('/store', 'NoticeController@store');
    Route::post('/delete', 'NoticeController@destroy');
});

Route::get('vod/index', 'VodController@index');
Route::prefix('vod')->middleware('auth:api')->group(function () {
    Route::post('/store', 'VodController@store');
    Route::post('/update', 'VodController@update');
    Route::post('/delete', 'VodController@destroy');
    Route::get('/getTags', 'TagItemController@index');
    Route::post('/updateTags', 'TagItemController@store');
});

Route::get('song-request/index', 'SongRequestController@index');
Route::prefix('song-request')->middleware('auth:api')->group(function () {
    Route::post('/store', 'SongRequestController@store');
    Route::post('/update', 'SongRequestController@update');
    Route::post('/delete', 'SongRequestController@destroy');
});

Route::get('playlist/index', 'PlaylistController@index');
Route::prefix('playlist')->middleware('auth:api')->group(function () {
    Route::post('/store', 'PlaylistController@store');
    Route::post('/update', 'PlaylistController@update');
    Route::post('/delete', 'PlaylistController@destroy');
});

