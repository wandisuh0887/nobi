<?php

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

Route::get('/login', function () {
    $response = [
        'code' => 401,
        'status' => "error",
        'message' => "Diperlukan otentikasi",
    ];
    return response()->json($response, $response['code']);
})->name('login');
