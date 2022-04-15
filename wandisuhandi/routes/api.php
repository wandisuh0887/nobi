<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => 'cors', 'prefix' => 'v1'], function() {
    Route::post('auth/login', [UserController::class,'login'])->middleware('throttle:15,1');
    Route::post('auth/register', [UserController::class,'register'])->middleware('throttle:15,1');
    Route::get('/quote', [UserController::class,'quote']);

    Route::group(['middleware' => 'auth:api'], function(){    
        Route::post('/transaction', [UserController::class,'transaction']);
        Route::post('/price/upload', [UserController::class,'uploadPrice']);
        Route::post('/price/low-high', [UserController::class,'priceLowHigh']);
        Route::post('/price/history', [UserController::class,'priceHistory']);
    });
});