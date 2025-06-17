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
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpeditureController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\RekapPresensiController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\HistoryTicketingController;
use App\Http\Controllers\SalaryPreviewController;


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

// HISTORY TICKETINGS
Route::prefix('history-ticketings')->group(function () {
    Route::get('/', [HistoryTicketingController::class, 'index']); // Tampilkan semua histori ticketing
    Route::get('/{id}', [HistoryTicketingController::class, 'show']); // Tampilkan histori berdasarkan ID
    Route::get('/driver/{driver_id}', [HistoryTicketingController::class, 'historyByDriver']); // Tampilkan histori berdasarkan ID driver
});

// PENGGAJIAN 
Route::middleware('auth:fo')->prefix('salary')->group(function () {
    Route::get('/previews', [SalaryPreviewController::class, 'index']); 
    Route::post('/previews/generate', [SalaryPreviewController::class, 'generatePreviews']); 
    Route::get('/preview/{userId}/{role}', [SalaryController::class, 'previewSalary']);
    Route::post('/store/{userId}/{role}', [SalaryController::class, 'storeSalary']);
    Route::get('/total/{userId}/{role}', [SalaryController::class, 'calculateTotalSalaryByUser']);
    Route::get('/all', [SalaryController::class, 'getAllSalaries']);
});


// ROLLING DRIVERS-++++
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
Route::get('/bookings/{order_id}', [BookingController::class, 'show']);
Route::put('/bookings/{id}', [BookingController::class, 'update']);
Route::delete('/bookings/{order_id}', [BookingController::class, 'destroy']);
Route::get('/count/bookings', [BookingController::class, 'countBooking']);

Route::post('/midtrans-notification', [MidtransNotificationController::class, 'midtransNotif']); // midtrans notification (webhook)
Route::get('/payment/sync-status/{order_id}', [PaymentController::class, 'syncStatus']);
Route::get('/orders/{order_id}/remaining-payment', [PaymentController::class,'getRemainingPaymentInfo']); // remaining payment info pembayaran ke 2
Route::post('/orders/{order_id}/remaining-payment', [PaymentController::class, 'startRemainingPayment']); // start remaining payment pembayaran ke 2

Route::get('/payment/orders', [PaymentController::class, 'index']);
Route::get('/payment/orders/{booking_id}', [PaymentController::class, 'show']);

Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/{slug}', [PackageController::class, 'show']);

Route::get('/vouchers', [VoucherController::class, 'index']);
Route::post('/vouchers', [VoucherController::class, 'store']);
Route::get('/vouchers/{id}', [VoucherController::class, 'show']);
Route::put('/vouchers/{id}', [VoucherController::class, 'update']);
Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy']); 

// GENERATE CONTENT
Route::prefix('content-generate')->group(function () {
    Route::post('/generate', [ContentGeneratorController::class, 'generate']);
    Route::post('/optimize', [ContentGeneratorController::class, 'optimize']);
    Route::post('/customoptimize', [ContentGeneratorController::class, 'CustomOptimize']);
    Route::post('/articleupdate/{id}', [ContentGeneratorController::class, 'updateArtikel']);
    Route::get('/draft', [ContentGeneratorController::class, 'read_all']);
    Route::post('/articledelete/{id}', [ContentGeneratorController::class, 'destroy']);
    Route::post('/storecontent', [ContentGeneratorController::class, 'store']);
    Route::get('/article/{id}', [ContentGeneratorController::class, 'read_one']);
    Route::get('/articleterbit', [ContentGeneratorController::class, 'read_all_terbit']);
    Route::get('/articlekonsep', [ContentGeneratorController::class, 'read_all_konsep']);
    Route::get('/articlesampah', [ContentGeneratorController::class, 'read_all_sampah']);
    Route::post('/article/{id}/gambar', [ContentGeneratorController::class, 'updateGambar']);
});

// Daily REPORT GENERATE
Route::prefix('dailyreports')->group(function () {
    Route::get('/alldaily', [DailyReportController::class, 'index']);
    Route::post('/generate-report', [DailyReportController::class, 'store']);
});

// EXPENDITURE REPORT GENERATE
Route::prefix('expenditures')->group(function () {
    Route::get('/all', [ExpeditureController::class, 'index']);
    Route::get('/{id}', [ExpeditureController::class, 'show']);
    Route::post('/generate', [ExpeditureController::class, 'storeformsalarie']);
    Route::post('/create', [ExpeditureController::class, 'store']);
    Route::put('/update/{id}', [ExpeditureController::class, 'update']);
    Route::delete('/delete/{id}', [ExpeditureController::class, 'destroy']);
});

// INCOME REPORT GENERATE
Route::prefix('income')->group(function () {
    Route::get('/all', [IncomeController::class, 'index']);
    Route::post('/create', [IncomeController::class, 'store']);
});

// REPORT GENERATE
Route::prefix('reports')->group(function () {
    Route::get('/bulan', [ReportController::class, 'index']);
    Route::post('/generate', [ReportController::class, 'generateAndStore']);
    Route::get('/triwulan', [ReportController::class, 'rekapMingguan']);
    Route::get('/tahun', [ReportController::class, 'rekapPerBulan']);
    Route::get('/statistik', [ReportController::class, 'statistik']);
});

// REKAP PRESENSI
Route::prefix('rekap-presensi')->group(function () {
    Route::post('/rekap', [App\Http\Controllers\RekapPresensiController::class, 'rekapPresensi']);
    Route::get('/all', [App\Http\Controllers\RekapPresensiController::class, 'index']);
    Route::get('/user/{userId}', [App\Http\Controllers\RekapPresensiController::class, 'showByUser']);
});
// Route::get('/rekap-presensi', [App\Http\Controllers\RekapPresensiController::class, 'rekapPresensi']);