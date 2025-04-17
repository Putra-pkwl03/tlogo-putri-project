<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:fo');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:fo');
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:fo');

Route::post('/checkout', [BookingController::class, 'checkout']);