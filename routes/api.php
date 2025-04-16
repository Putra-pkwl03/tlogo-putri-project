<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::post('/fo/login', [AuthController::class, 'login']);
Route::get('/fo/profile', [AuthController::class, 'profile'])->middleware('auth:fo');
Route::post('/fo/logout', [AuthController::class, 'logout'])->middleware('auth:fo');
Route::post('/fo/refresh', [AuthController::class, 'refresh'])->middleware('auth:fo');
Route::post('/login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
// Route::post('/users', [AuthController::class, 'store']);
// Route::get('/users', [AuthController::class, 'index']);
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/users', [Controller::class, 'store']);
//     Route::get('/users', [Controller::class, 'index']);
// });
Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});