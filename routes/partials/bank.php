<?php

use App\Http\Middleware\BankApiCheck;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => BankApiCheck::class, 'namespace' => 'Bank'], function () {
    Route::post('student', 'StudentController@find');
    Route::post('fees/paynow', 'PaymentController@store');
    Route::post('fees/payment-status', 'PaymentController@paymentStatus');
    // Route::post('fees/payment-retry', 'PaymentController@paymentRetry');
    Route::post('fees/payment-reverse', 'PaymentController@paymentReverse');
});

// Route::group(['namespace' => 'Bank'], function () {
//     Route::post('student', 'StudentController@find');
//     Route::post('fees/paynow', 'PaymentController@store');
//     Route::post('fees/payment-status', 'PaymentController@paymentStatus');
// });