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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::get('cc', function () { return view('cc'); })->name('cc.get');
//Route::post('/cc', function () { return view('cciframe'); })->name('cc.post');
Route::get('/cciframe', 'SubscriptionController@cc')->name('cciframe.get');
Route::post('/cc', 'SubscriptionController@ccPost')->name('cc.post');
Route::post('/cc/calback', 'SubscriptionController@ccCalback')->name('cc.callback');

Route::get('paypal', function () { return view('paypal'); })->name('paypal.get');

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/subscription/', 'SubscriptionController@cancel')->name('subscription.cancel');
Route::post('/subscription/{subscription_id}', 'SubscriptionController@store')->name('subscription.store');

Route::get('/pdf', 'HomeController@pdf')->name('pdf');


