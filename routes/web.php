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

Route::domain('{account}.myapp.com')->group(function ($account) {
    return $account;
//    Route::get('user/{id}', function ($account, $id) {
//        //
//    });
});



//Route::group([
//    'prefix'     => '/{tenant}',
//    'middleware' => \App\Http\Middleware\IdentifyTenant::class,
//    'as'         => 'tenant:',
//], function () {
//    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
//});


// Stock form for Heidelpay
Route::get('/cciframe', 'SubscriptionController@cciframe')->name('cciframe');
Route::post('/cciframe/response', 'SubscriptionController@cciframeResponse')->name('cciframe.response');


// WireCard
Route::get('/wirecard', function () { return view('wirecard'); })->name('wirecard.get');
Route::post('/wirecard', 'SubscriptionController@wirecardPost')->name('wirecard.post');

// Wirecard HPP
Route::get('/wirecard_form', 'SubscriptionController@wirecardGet')->name('wirecardform.get');

// Custom form for Heidelpay
Route::get('/cc', function () { return view('cc'); })->name('cc.get');
Route::post('/cc', 'SubscriptionController@ccPost')->name('cc.post');
Route::post('/cc/calback', 'SubscriptionController@ccCalback')->name('cc.callback');

Route::get('paypal', function () { return view('paypal'); })->name('paypal.get');

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/subscription/', 'SubscriptionController@cancel')->name('subscription.cancel');
Route::post('/subscription/{subscription_id}', 'SubscriptionController@store')->name('subscription.store');

Route::get('/pdf', 'HomeController@pdf')->name('pdf');


