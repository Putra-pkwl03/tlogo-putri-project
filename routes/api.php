<?php


use App\Http\Controllers\AuthController;

Route::post('/fo/login', [AuthController::class, 'login']);
Route::get('/fo/profile', [AuthController::class, 'profile'])->middleware('auth:fo');
Route::post('/fo/logout', [AuthController::class, 'logout'])->middleware('auth:fo');
Route::post('/fo/refresh', [AuthController::class, 'refresh'])->middleware('auth:fo');