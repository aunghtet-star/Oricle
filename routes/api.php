<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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
Route::namespace('Api')->group(function(){
    Route::post('/register','AuthController@register');
    Route::post('/login','AuthController@login');
});

Route::middleware('auth:api')->namespace('Api')->group(function(){
    Route::post('/logout','AuthController@logout');
    Route::get('/profile','PageController@profile');
    Route::get('/transactions','PageController@transaction');
    Route::get('/transactions/{id}','PageController@transactionDetail');
    Route::get('/notifications','PageController@notification');
    Route::get('/notifications/{id}','PageController@notificationDetail');
    Route::get('/toaccountVerify','PageController@toaccountVerify');
    Route::post('/transfer/confirm','PageController@transferConfirm');
    Route::post('/transfer/complete','PageController@transferComplete');
    Route::post('/scan_and_pay_confirm','PageController@scanAndPayConfirm');
    Route::post('/scan_and_pay_complete','PageController@scanAndPayComplete');
});
