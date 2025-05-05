<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\Salaries;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function calculateSalary(Request $request, $userId)
    {
        // Ambil data user berdasarkan $userId (misalnya driver atau owner)
        $user = User::findOrFail($userId);

        // Ambil data pemesanan yang relevan (misalnya yang sudah dibayar)
        $bookings = Booking::where('user_id', $userId)
            ->where('status', 'completed')
            ->get();

        $totalSalary = 0;

        // Hitung gaji berdasarkan 30% untuk owner dan 70% untuk driver
        foreach ($bookings as $booking) {
            $package = TourPackage::findOrFail($booking->package_id);
            $packagePrice = $package->price;

            if ($user->role === 'Owner') {
                // 30% untuk owner
                $totalSalary += ($packagePrice * 0.30);
            } elseif ($user->role === 'Driver') {
                // 70% untuk driver
                $totalSalary += ($packagePrice * 0.70);
            }
        }

        // Simpan gaji ke tabel salaries
        $salary = Salaries::create([
            'nama' => $user->name,
            'role' => $user->role,
            'no_lambung' => $user->plat_jeep,
            'salarie' => $totalSalary,
            'total_salary' => $totalSalary,
            'payment_date' => Carbon::now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Salary calculated successfully',
            'salary' => $salary
        ], 200);
    }

    public function salaryHistory($userId)
    {
        // Ambil riwayat gaji untuk user tertentu
        $salaryHistory = Salaries::where('salaries.user_id', $userId)
            ->get();

        return response()->json([
            'salary_history' => $salaryHistory
        ], 200);
    }
}
