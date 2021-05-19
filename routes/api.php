<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\SearchController;
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

Route::group(['prefix' => 'v1'], function () {

    Route::get('regions', [SearchController::class, 'regions']);
    Route::get('postal-codes', [SearchController::class, 'postalCodes']);
    Route::get('town-for-postal-code/{code}', [SearchController::class, 'townForPostalCode']);
    
});