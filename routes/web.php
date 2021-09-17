<?php

use Illuminate\Support\Facades\Auth;
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


Auth::routes();

Route::middleware('auth')->namespace('frontEnd')->group(function () {
    Route::get('/', 'PageController@home')->name('home');
    Route::get('/profile','PageController@profile')->name('profile');
    Route::get('/update','PageController@updatePassword')->name('updatePassword');
    Route::post('/update','PageController@updatePasswordStore')->name('updatePassword.store');
    
    Route::get('/wallet','PageController@wallet')->name('wallet');
    Route::get('/transfer','PageController@transfer')->name('transfer');
    Route::post('/transfer/confirm','PageController@transferconfirm')->name('transfer.confirm');
    Route::post('/transfer/complete','PageController@transfercomplete')->name('transfer.complete');
    Route::post('/password_check','PageController@password_check')->name('password_check');

    Route::get('/transaction','PageController@transaction');
    Route::get('/transactionDetail/{uuid}','PageController@transactionDetail');
    
    Route::get('/toaccountVerify','PageController@toaccountVerify');

    Route::get('/receive_qr','PageController@receive_qr');
    Route::get('/scan_qr','PageController@scanQr');
    Route::get('/scan_and_pay','PageController@scan_and_pay');
    Route::post('/scan_and_pay_confirm','PageController@scan_and_pay_confirm')->name('scan_and_pay.confirm');
    Route::post('/scan_and_pay_complete','PageController@scan_and_pay_complete')->name('scan_and_pay.confirm');

    Route::get('/notification','NotificationController@index');
    Route::get('/notificationDetail/{id}','NotificationController@show');

});

Route::get('/admin/login', 'Auth\AdminLoginController@showLoginForm');
Route::post('/admin/login', 'Auth\AdminLoginController@login')->name('admin.login');
Route::post('/admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
