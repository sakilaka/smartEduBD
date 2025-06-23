<?php

use App\Http\Middleware\CheckContentType;
use Illuminate\Support\Facades\Route;

/** guardian panel **/
Route::prefix('guardian')
    ->namespace('Api')
    ->middleware([CheckContentType::class])
    ->group(base_path('routes/partials/guardian.php'));

/** bank api **/
Route::prefix('bank')
    ->namespace('Api')
    ->middleware([CheckContentType::class])
    ->group(base_path('routes/partials/bank.php'));

Route::get('mobile-app-version', 'Api\LibController@mobileAppVersion');
