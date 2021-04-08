<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\HomeController;

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
Route::post('login', [HomeController::class, 'login']);



Route::middleware('auth:api')->group(function () {
    Route::post('panic/create', [HomeController::class, 'create_panic']);
    Route::get('panic/get', [HomeController::class, 'panic_history']);
    Route::post('panic/cancel', [HomeController::class, 'cancel_panic']);
});
