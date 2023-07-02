<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/store', [App\Http\Controllers\API\SalaryController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/show/{user}', [App\Http\Controllers\API\SalaryController::class, 'show']);
    Route::delete('/show/{user}', [App\Http\Controllers\API\SalaryController::class, 'destroy']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});


Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
