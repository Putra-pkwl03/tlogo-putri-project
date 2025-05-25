<?php

namespace App\Http\Controllers;

use App\Models\Ticketing;
use App\Models\Salaries;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function calculateSalary()
    {
        $tickets = Ticketing::with([
            'booking.package',
            'jeep.driver',
            'jeep.owner',
        ])->get();

        foreach ($tickets as $ticket) {
            $booking = $ticket->booking;
            $package = $booking?->package;
            $jeep = $ticket->jeep;
            $driver = $jeep?->driver;
            $owner = $jeep?->owner;

            if (!$booking || !$package || !$driver) {
                Log::warning('Skipping ticket due to missing relation', [
                    'ticket_id' => $ticket->id,
                    'booking_exists' => (bool) $booking,
                    'package_exists' => (bool) $package,
                    'driver_exists' => (bool) $driver,
                ]);
                continue;
            }

            $referralType = $booking->referral_code;
            $price = $package->price;
            $kas = 0;
            $operasional = 0;
            $bonusDriver = 0;
            $referralCut = 0;

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
                    Log::warning('Unknown package price', ['price' => $price]);
                    continue 2;
            }

            if ($referralType === 'rn') {
                $referralCut = 50000;
            } elseif ($referralType === 'op') {
                $kas = 25000;
                $operasional = 25000;
                $bonusDriver = 30000;
            }

            $net = $price - ($kas + $operasional + $referralCut);
            $driverShare = ($net * 0.7) + $bonusDriver;
            $ownerShare = $net * 0.3;

            // Cek duplikat salary
            $existingDriverSalary = Salaries::where('user_id', $driver->id)
                ->where('ticketing_id', $ticket->id)
                ->first();

            if ($existingDriverSalary) {
                Log::info('Driver salary already exists, skipping', [
                    'user_id' => $driver->id,
                    'ticketing_id' => $ticket->id,
                ]);
                continue;
            }

            try {
                $salariDriver = Salaries::create([
                    'user_id' => $driver->id,
                    'ticketing_id' => $ticket->id,
                    'nama' => $driver->name,
                    'role' => $driver->role,
                    'no_lambung' => $driver->plat_jeep ?? '-',
                    'kas' => $kas,
                    'operasional' => $operasional,
                    'salarie' => $driverShare,
                    'total_salary' => $driverShare,
                    'payment_date' => Carbon::now()->toDateString(),
                    'status' => 'belum',
                ]);

                Log::info('Driver salary saved', ['id' => $salariDriver->id]);
            } catch (\Exception $e) {
                Log::error('Failed to save driver salary', [
                    'error' => $e->getMessage(),
                    'data' => [
                        'user_id' => $driver->id,
                        'ticketing_id' => $ticket->id,
                        'nama' => $driver->name,
                        'role' => $driver->role,
                        'no_lambung' => $driver->plat_jeep ?? '-',
                        'salarie' => $driverShare,
                        'total_salary' => $driverShare,
                    ],
                ]);
                continue;
            }

            // Cek dan simpan gaji owner
            if ($owner) {
                $existingOwnerSalary = Salaries::where('user_id', $owner->id)
                    ->where('ticketing_id', $ticket->id)
                    ->first();

                if ($existingOwnerSalary) {
                    Log::info('Owner salary already exists, skipping', [
                        'user_id' => $owner->id,
                        'ticketing_id' => $ticket->id,
                    ]);
                    continue;
                }

                try {
                    $salariOwner = Salaries::create([
                        'user_id' => $owner->id,
                        'ticketing_id' => $ticket->id,
                        'nama' => $owner->name,
                        'role' => $owner->role,
                        'no_lambung' => $driver->plat_jeep ?? '-',
                        'kas' => $kas,
                        'operasional' => $operasional,
                        'salarie' => $ownerShare,
                        'total_salary' => $ownerShare,
                        'payment_date' => Carbon::now()->toDateString(),
                        'status' => 'belum',
                    ]);

                    Log::info('Owner salary saved', ['id' => $salariOwner->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to save owner salary', [
                        'error' => $e->getMessage(),
                        'data' => [
                            'user_id' => $owner->id,
                            'ticketing_id' => $ticket->id,
                            'nama' => $owner->name,
                            'role' => $owner->role,
                            'no_lambung' => $driver->plat_jeep ?? '-',
                            'salarie' => $ownerShare,
                            'total_salary' => $ownerShare,
                        ],
                    ]);
                    continue;
                }
            }
        }

        return response()->json([
            'message' => 'Salary calculation completed. Check logs for errors or skipped records.',
        ]);
    }

    public function salaryHistory($userId)
    {
        $history = Salaries::where('user_id', $userId)->get();

        return response()->json([
            'salary_history' => $history
        ]);
    }

    public function updateSalaryStatus()
    {
        $updated = Salaries::where('status', 'belum')->update([
            'status' => 'diterima',
            'payment_date' => now()
        ]);

        return response()->json([
            'message' => 'Status semua gaji yang belum diterima telah diubah menjadi diterima.',
            'total_updated' => $updated
        ]);
    }

}
