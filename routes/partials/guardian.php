<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'Guardian\Auth\LoginController@login');

/** forgot password **/
// Route::post('send-otp', 'OTPController@sendOTP');
// Route::post('check-otp', 'OTPController@checkOTP');
// Route::post('forgot-password', 'StudentController@passwordChange');

Route::get('initial-data', 'LibController@index');
// Route::get('content', 'HomeController@content');
Route::get('notices', 'HomeController@notices')->middleware('auth:guardianApi');

/** logged user can acces this routes **/
Route::group(['middleware' => 'auth:guardianApi', 'namespace' => 'Guardian'], function () {

    Route::post('delete-account', 'Auth\LoginController@deleteAccount');
    Route::post('logout', 'Auth\LoginController@logout');

    /** dashboard **/
    Route::get('get-notices', 'HomeController@getNotices');

    /** guardian && student **/
    Route::get('profile', 'GuardianController@profile');
    Route::post('profile/update', 'GuardianController@update');
    Route::post('change-password', 'GuardianController@passwordChange');

    /** student **/
    Route::get('students', 'StudentController@index');
    Route::post('switch-student', 'StudentController@switchStudent');
    Route::get('student/profile', 'StudentController@profile');
    Route::post('student/update', 'StudentController@update');

    /** fees && payments **/
    Route::get('fees', 'FeesController@index');
    Route::get('fees/dues', 'FeesController@duesFeesLists');
    Route::get('fees/tuitions', 'FeesController@tuitionFees');
    Route::get('fees/history', 'PaymentController@index');
    Route::post('fees/check-dependent-head', 'PaymentController@checkDependentHead');
    Route::post('fees/paynow', 'PaymentController@store');
    Route::post('fees/pay-invoice', 'PaymentController@payInvoice');
    Route::post('fees/view-invoice', 'PaymentController@invoice');

    /** result **/
    Route::get('get-exam', 'ResultController@examLists');
    Route::post('search-result', 'ResultController@result');
    Route::post('download-marksheet', 'ResultController@downloadMarkSheet');
});
