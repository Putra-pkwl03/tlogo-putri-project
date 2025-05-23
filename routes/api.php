<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JeepController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ContentGeneratorController;
use App\Http\Controllers\TicketingController;
use App\Http\Controllers\DriverRotationController;

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
    Route::get('/owner/{ownerId}', [JeepController::class, 'showByOwner']);
    Route::get('/driver/{driverId}', [JeepController::class, 'showByDriver']);
    Route::put('/update/{id}', [JeepController::class, 'update']); // Update
    Route::delete('/delete/{id}', [JeepController::class, 'delete']); // Delete
});

// TICKET CRUD GROUP
Route::prefix('ticketings')->group(function () {
    Route::get('/all', [TicketingController::class, 'index']); // Semua tiket
    Route::post('/create', [TicketingController::class, 'store']); // Create
    Route::get('/id/{id}', [TicketingController::class, 'show']); // Berdasarkan ID
    Route::delete('/delete/{id}', [TicketingController::class, 'destroy']); // Delete
});

// PENGGAJIAN 
Route::get('/salary/calculate/{userId}', [SalaryController::class, 'calculateSalary']);
Route::get('/salary/history/{userId}', [SalaryController::class, 'salaryHistory']);

// ROLLING DRIVERS
Route::prefix('driver-rotations')->group(function () {
    Route::get('/', [DriverRotationController::class, 'index']); // lihat rotasi harian
    Route::post('/generate', [DriverRotationController::class, 'generate']); // buat rotasi besok
    Route::post('/{id}/skip', [DriverRotationController::class, 'skip']); // tandai driver skip
    Route::post('/{id}/assign', [DriverRotationController::class, 'assign']);
});


// BOOKING / MIDTRANS
// Route::apiResource('bookings', BookingController::class) ->only(['index', 'store', 'show', 'update']);; // crud
Route::get('/bookings', [BookingController::class, 'index']); //untuk menampilkan data pemesanan. jangan llupa migrate dulu semua seedernya
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
Route::put('/bookings/{id}', [BookingController::class, 'update']);
// Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

Route::post('/midtrans-notification', [MidtransNotificationController::class, 'midtransNotif']); // midtrans notification (webhook)
Route::get('/orders/{order_id}/remaining-payment', [PaymentController::class,'getRemainingPaymentInfo']); // remaining payment info pembayaran ke 2
Route::post('/orders/{order_id}/remaining-payment', [PaymentController::class, 'startRemainingPayment']); // start remaining payment pembayaran ke 2

Route::get('/payment/orders', [PaymentController::class, 'index']);
Route::get('/payment/orders/{booking_id}', [PaymentController::class, 'show']);

Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/{id}', [PackageController::class, 'show']);

// GENERATE CONTENT
Route::prefix('content-generate')->group(function () {
    Route::post('/generate', [ContentGeneratorController::class, 'generate']);
    Route::post('/optimize', [ContentGeneratorController::class, 'optimize']);
    Route::post('/articleupdate/{id}', [ContentGeneratorController::class, 'updateArtikel']);
    Route::get('/draft', [ContentGeneratorController::class, 'read_all']);
    Route::delete('/articledelete/{id}', [ContentGeneratorController::class, 'destroy']);
    Route::post('/storecontent', [ContentGeneratorController::class, 'store']);
    Route::get('/article/{id}', [ContentGeneratorController::class, 'read_one']);
});

// REPORT GENERATE
Route::get('/generate-report', [ReportController::class, 'calculatereport']);
