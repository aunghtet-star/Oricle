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


Route::prefix('admin')->name('admin.')->namespace('backEnd')->middleware('auth:admin_user')->group(function () {
    Route::get('/', 'PageController@home')->name('home');
    Route::resource('users', 'AdminUserController');
    Route::get('/users/datatables/ssd', 'AdminUserController@ssd');

    Route::resource('n_users', 'UserController');
    Route::get('/n_users/datatables/ssd', 'UserController@ssd');

    Route::get('wallet', 'WalletController@index')->name('wallet.index');
    Route::get('/wallet/datatables/ssd', 'WalletController@ssd');

    Route::get('wallet/add', 'WalletController@add')->name('wallet.add');
    Route::get('wallet/reduce', 'WalletController@reduce')->name('wallet.reduce');

    Route::post('/wallet/add/store', 'WalletController@store')->name('wallet.add.store');
    Route::post('wallet/reduce/store', 'WalletController@remove')->name('wallet.reduce.store');

});
