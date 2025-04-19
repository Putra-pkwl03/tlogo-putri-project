<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/fo/profile', [AuthController::class, 'profile'])->middleware('auth:fo');
Route::post('register', [AuthController::class, 'register'])->middleware('auth:fo');
Route::post('/users', [Controller::class, 'store']);
Route::get('/users', [Controller::class, 'index']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
});