<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;



// AUTH GROUP
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// USER CRUD GROUP
Route::prefix('users')->middleware('auth:fo')->group(function () {
    Route::post('/register', [UserController::class, 'register']); // Create user
    Route::get('/me', [UserController::class, 'me']); // Read me
    Route::get('/all', [UserController::class, 'all']); // Read all
    Route::get('/by-role', [UserController::class, 'getUsersByRole']); // Read by role
    Route::put('/update/{id}', [UserController::class, 'update']);  // Update user
    Route::put('/update', [UserController::class, 'update']);  // Update user
    Route::delete('/delete/{id}', [UserController::class, 'delete']); // Delete user
});


// BOOKING / MIDTRANS
Route::post('/orders/checkout', [BookingController::class, 'checkout']);
Route::post('/midtrans-notification', [BookingController::class, 'midtransNotif']);
Route::get('/orders/{order_id}/remaining-payment', [PaymentController::class, 'getRemainingPaymentInfo']);
Route::post('/orders/{order_id}/remaining-payment', [PaymentController::class, 'startRemainingPayment']);
Route::post('/checkout', [BookingController::class, 'checkout']);





