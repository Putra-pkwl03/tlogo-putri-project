<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use App\Models\User;
use App\Models\Salaries;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function calculateSalary(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $tickets = Ticketing::with(['booking.package'])
            ->where('driver_id', $userId)
            ->get();

        $totalDriverSalary = 0;
        $totalOwnerSalary = 0;

        foreach ($tickets as $ticket) {
            $booking = $ticket->booking;

            if (!$booking || !$booking->package) {
                continue;
            }

            $package = $booking->package;
            $referralType = $booking->referral_code; // langsung dari kolom

            $price = $package->price;
            $kas = 0;
            $operasional = 0;
            $bonusDriver = 0;
            $referralCut = 0;

            // Tentukan kas dan operasional berdasarkan harga paket
            switch ($price) {
                case 400000:
                    $kas = 30000;
                    $operasional = 35000;
                    break;
                case 450000:
                    $kas = 35000;
                    $operasional = 40000;
                    break;
                case 550000:
                    $kas = 40000;
                    $operasional = 45000;
                    break;
                default:
                    continue 2;
            }

            // Referral logic tanpa model
            if ($referralType === 'rn') {
                $referralCut = 50000;
            } elseif ($referralType === 'op') {
                $kas = 25000;
                $operasional = 25000;
                $bonusDriver = 30000;
            }

            // Perhitungan gaji bersih
            $net = $price - ($kas + $operasional + $referralCut);
            $driverShare = ($net * 0.7) + $bonusDriver;
            $ownerShare = $net * 0.3;

            $totalDriverSalary += $driverShare;
            $totalOwnerSalary += $ownerShare;
        }

        // Simpan gaji driver
        Salaries::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'role' => $user->role,
            'no_lambung' => $user->plat_jeep ?? '-',
            'salarie' => $totalDriverSalary,
            'total_salary' => $totalDriverSalary,
            'payment_date' => Carbon::now()->toDateString(),
        ]);

        // Simpan gaji owner jika user ini adalah owner
        if ($user->role === 'owner') {
            Salaries::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'role' => $user->role,
                'no_lambung' => $user->plat_jeep ?? '-',
                'salarie' => $totalOwnerSalary,
                'total_salary' => $totalOwnerSalary,
                'payment_date' => Carbon::now()->toDateString(),
            ]);
        }

        return response()->json([
            'message' => 'Salary calculated successfully',
            'driver_salary' => $totalDriverSalary,
            'owner_salary' => $totalOwnerSalary
        ]);
    }

    public function salaryHistory($userId)
    {
        $history = Salaries::where('user_id', $userId)->get();

        return response()->json([
            'salary_history' => $history
        ]);
    }
}
