<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\Ticketing;
use App\Models\Jeep;
use App\Models\DailyReport;
use App\Models\Salaries;

class ReportController extends Controller
{
    public function calculatereport()
    {
        // Mapping untuk pembagian cash dan oop berdasarkan nama paket
        $cashValues = [
            'tour package 1' => 30000,
            'tour package 2' => 35000,
            'tour package 3' => 35000,
        ];

        $oopValues = [
            'tour package 1' => 35000,
            'tour package 2' => 40000,
            'tour package 3' => 40000,
        ];

        // Ambil semua ticketing beserta relasi terkait agar lebih efisien
        $ticketings = Ticketing::with(['booking', 'booking.package', 'jeep', 'salaries'])->get();

        foreach ($ticketings as $ticketing) {
            $booking = $ticketing->booking;
            $tour_package = $booking?->package;
            $jeep = $ticketing->jeep;
            $salaries = $ticketing->salaries;

            if (!$booking || !$tour_package || !$jeep || !$salaries) {
                continue;
            }

            if (!$booking || !$tour_package || !$jeep || !$salaries) {
                \Log::info('Data tidak lengkap', [
                    'booking' => $booking?->booking_id,
                    'tour_package' => $tour_package?->package_id ?? null,
                    'jeep' => $jeep?->id ?? null,
                    'salaries' => $salaries?->salarie_id ?? null
                ]);
                continue;
            }

            // Cek apakah laporan untuk booking ini sudah dibuat agar tidak duplikat
            if (DailyReport::where('booking_id', $booking->booking_id)->exists()) {
                continue;
            }

            $package = strtolower(trim($tour_package->package_name));

            // Hitung pembagian
            $marketing = !empty($booking->referral_code) ? 50000 : 0;
            $cash = $cashValues[$package] ?? 0;
            $oop = $oopValues[$package] ?? 0;
            $pay_driver = $marketing + $cash + $oop;
            $driver_accept = $booking->gross_amount - $pay_driver;

            // Simpan ke tabel DailyReport
            \Log::info('Menyimpan DailyReport untuk booking_id: ' . $booking->booking_id);
            DailyReport::create([
                'booking_id'     => $booking->booking_id,
                'stomach_no'     => $jeep->no_lambung,
                'touring_packet' => $tour_package->package_name,
                'code'           => '', // kosong dari tamplatenya
                'marketing'      => $marketing,
                'cash'           => $cash,
                'oop'            => $oop,
                'pay_driver'     => $pay_driver,
                'driver_accept'  => $driver_accept,
                'paying_guest'   => $booking->gross_amount,
                'total_cash'     => $cash + $oop,
                'price'          => 0, // kosong dari tamplatenya
                'amount'         => 0, // Sama seperti di atas
                'information'    => 'INDUK',
                'arrival_time'   => $booking->tour_date,
                'salaries_id'    => $salaries->salaries_id,
            ]);
        }

        return response()->json(['message' => 'Laporan berhasil dibuat.']);
    }
    


    /**
     * Show the form for creating a new resource.
     */
    public function recapdaily()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
