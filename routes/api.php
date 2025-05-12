<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JeepController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MidtransNotificationController;



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

// JEEP CRUD GROUP
Route::prefix('jeeps')->group(function () {
    Route::post('/create', [JeepController::class, 'create']); // Create
    Route::get('/all', [JeepController::class, 'index']); // Semua jeep
    Route::get('/id/{id}', [JeepController::class, 'showById']); // Berdasarkan ID
    Route::get('/status/{status}', [JeepController::class, 'showByStatus']); // Berdasarkan Status
    Route::put('/update/{id}', [JeepController::class, 'update']); // Update
    Route::delete('/delete/{id}', [JeepController::class, 'delete']); // Delete
});

// BOOKING / MIDTRANS
Route::apiResource('bookings', BookingController::class) ->only(['index', 'store', 'show', 'update']);; // crud
Route::post('/midtrans-notification', [MidtransNotificationController::class, 'midtransNotif']); // midtrans notification (webhook)
Route::get('/orders/{order_id}/remaining-payment', [PaymentController::class, 'getRemainingPaymentInfo']); // remaining payment info pembayaran ke 2
Route::post('/orders/{order_id}/remaining-payment', [PaymentController::class, 'startRemainingPayment']); // start remaining payment pembayaran ke 2

