<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::view('/', 'layouts.frontend_app');

/**-------------------------------------------------
 ** PAYMENT WITH SSL & SPAY
 ** ---------------------------------------------**/
Route::namespace('PaymentGateway')->group(function () {
    Route::post('/success-from-ssl', 'SslCommerzController@success');
    Route::post('/failed-from-ssl', 'SslCommerzController@failed');
    Route::post('/cancel-from-ssl', 'SslCommerzController@cancel');
    Route::post('/ipn', 'SslCommerzController@ipn');

    // SPAY
    Route::get('/success-from-spay', 'ShurjoPayController@success');
    Route::get('/cancel-from-spay', 'ShurjoPayController@cancel');
    Route::get('/ipn-from-spay', 'ShurjoPayController@ipn');
});

Route::view('/ssl-response', 'layouts.ssl_response');

// AUTH VERIFY FALSE
Auth::routes(['verify' => false]);

// FOR STORAGE LINKED IN PUBLIC FOLDER
Route::get('/sym', function () {
    File::link(storage_path('app/public'), public_path('storage'));
    return response()->json(['message' => 'Link Create Successfully!'], 200);
});

// CACHE CLEAR
Route::get('/clear', function () {
    Artisan::call('optimize:clear'); // Clears compiled views, route, config, and services
    Artisan::call('cache:clear');    // Clears application cache
    Artisan::call('route:clear');    // Clears route cache
    Artisan::call('config:clear');   // Clears config cache
    Artisan::call('view:clear');     // Clears compiled views
    Artisan::call('event:clear');    // Clears cached events (if any)

    return response()->json(['message' => 'All Laravel caches cleared successfully!'], 200);
});

Route::get('/test-log', function() {
    Log::info('This is a test log message');
    return response()->json(['message' => 'Check your logs']);
});