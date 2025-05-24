<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\Ticketing;
use App\Models\Jeep;
use App\Models\DailyReport;
use App\Models\Salaries;

class DailyReportController extends Controller
{
    public function index()
    {
        $dailyreport = DailyReport::all();
        return response()->json($dailyreport);
    }

    public function calculatereport()
    {
        // Mapping untuk pembagian cash dan oop berdasarkan nama paket
        $cashValues = [
            'tour package 1' => 30000,
            'tour package 2' => 35000,
            'tour package 3' => 35000,
            'tour package 4' => 35000,
            'tour package 5' => 40000,
        ];

        $oopValues = [
            'tour package 1' => 35000,
            'tour package 2' => 40000,
            'tour package 3' => 40000,
            'tour package 4' => 40000,
            'tour package 5' => 45000,
        ];

        $marketingValues = [
            'rn' => 50000,
            'op' => 20000,
        ];

        // Ambil semua salaries beserta relasi terkait agar lebih efisien
        $salaries = Salaries::with([
            'ticketing.booking.package',
            'ticketing.jeep'
        ])->get();

        foreach ($salaries as $salary) {
            $ticketing = $salary->ticketing;
            $booking = $ticketing?->booking;
            $tour_package = $booking?->package;
            $jeep = $ticketing?->jeep;

            if (!$booking || !$tour_package || !$jeep) {
                \Log::info('Data tidak lengkap', [
                    'booking' => $booking?->booking_id,
                    'tour_package' => $tour_package?->package_id ?? null,
                    'jeep' => $jeep?->id ?? null,
                    'salaries' => $salary->salaries_id ?? null
                ]);
                continue;
            }

            // Cek apakah laporan untuk booking ini sudah dibuat agar tidak duplikat
            if (DailyReport::where('booking_id', $booking->booking_id)->exists()) {
                continue;
            }

            $package = strtolower(trim($tour_package->package_name));

            // Hitung pembagian
            $referral = trim(strtolower($booking->referral_code ?? ''));
            $marketing = 0;
            foreach ($marketingValues as $key => $value) {
                if (stripos($referral, $key) !== false) {
                    $marketing = $value;
                    break; // Ambil nilai pertama yang cocok
                }
            }

            $cash = $cashValues[$package] ?? 0;
            $oop = $oopValues[$package] ?? 0;

            if ($cash === 0 || $oop === 0) {
                \Log::warning('Paket tidak ditemukan dalam mapping', ['package' => $package]);
                continue;
            }

            $pay_driver = $marketing + $cash + $oop;
            $driver_accept = $booking->gross_amount - $pay_driver;

            \Log::info('Menyimpan DailyReport untuk booking_id: ' . $booking->booking_id);
            $reportdaily = DailyReport::create([
                'booking_id'     => $booking->booking_id,
                'stomach_no'     => $jeep->no_lambung,
                'touring_packet' => $tour_package->package_name,
                'code'           => '', // kosong dari template
                'marketing'      => $marketing,
                'cash'           => $cash,
                'oop'            => $oop,
                'pay_driver'     => $pay_driver,
                'driver_accept'  => $driver_accept,
                'paying_guest'   => $booking->gross_amount,
                'total_cash'     => $cash + $oop,
                'price'          => 0, // kosong dari template
                'amount'         => 0, // kosong dari template
                'information'    => 'INDUK',
                'salaries_id'    => $salary->salaries_id,
                "arrival_time"   =>  $booking->tour_date
            ]);
        }

        return response()->json([
            'message' => 'Laporan berhasil dibuat.',
            'data'    => $reportdaily]);
    }

    public function show($id)
    {
        $dailyreport = DailyReport::with(['booking.package', 'jeep', 'salaries'])->find($id);

        if (!$dailyreport) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($dailyreport);
    }

}