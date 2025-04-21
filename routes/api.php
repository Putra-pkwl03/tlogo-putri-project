<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

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

Route::post('/orders/checkout', [BookingController::class, 'checkout']);
Route::post('/midtrans-notification', [BookingController::class, 'midtransNotif']);
Route::get('/orders/{order_id}/remaining-payment', [PaymentController::class, 'getRemainingPaymentInfo']);
Route::post('/orders/{order_id}/remaining-payment', [PaymentController::class, 'startRemainingPayment']);
