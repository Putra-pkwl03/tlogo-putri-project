<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/fo/profile', [AuthController::class, 'profile'])->middleware('auth:fo');
Route::post('register', [AuthController::class, 'register'])->middleware('auth:fo');
Route::post('/users', [AuthController::class, 'store']);
Route::get('/users', [AuthController::class, 'index']);
Route::post('/logout', [AuthController::class, 'logout']);
<<<<<<< HEAD
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
});


Route::post('/checkout', [BookingController::class, 'checkout']);
Route::post('/midtrans-notification', [BookingController::class, 'midtransNotif']);

=======
Route::middleware('auth:api')->group(function () { });
Route::get('/me', [AuthController::class, 'me']);
Route::get('/users-by-role', [AuthController::class, 'getUsersByRole']);
>>>>>>> beb2aa0 (menambahkan get data user)
