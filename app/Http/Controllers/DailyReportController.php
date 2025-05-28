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

    // menghitung laporan harian
    public function calculate()
    {
        $marketingValues = [
            'rn' => 50000,
            'op' => 20000,
        ];

        $informationValues = [
            20000 => 'RN',
            50000 => 'OP',
        ];

        $salaries = Salaries::with([
            'ticketing.booking.package',
            'ticketing.jeep'
        ])->where('role', 'Driver')->get();

        $calculated = [];

        foreach ($salaries as $salary) {
            $ticketing = $salary->ticketing;
            $booking = $ticketing?->booking;
            $tour_package = $booking?->package;
            $jeep = $ticketing?->jeep;

            if (!$booking || !$tour_package || !$jeep) {
                continue;
            }

            $package = strtolower(trim($tour_package->package_name));
            $referral = trim(strtolower($booking->referral ?? ''));
            $marketing = 0;
            foreach ($marketingValues as $key => $value) {
                if (stripos($referral, $key) !== false) {
                    $marketing = $value;
                    break;
                }
            }

            $information = $informationValues[$marketing] ?? 'INDUK';
            $cash = $salary->kas;
            $oop = $salary->operasional;

            if ($cash === 0 || $oop === 0) {
                continue;
            }

            $pay_driver = $marketing + $cash + $oop;
            $driver_accept = $booking->gross_amount - $pay_driver;

            $calculated[] = [
                'salaries_id'    => $salary->salaries_id,
                'booking_id'     => $booking->booking_id,
                'stomach_no'     => $jeep->no_lambung,
                'touring_packet' => $tour_package->package_name,
                'code'           => '',
                'marketing'      => $marketing,
                'cash'           => $cash,
                'oop'            => $oop,
                'pay_driver'     => $pay_driver,
                'driver_accept'  => $driver_accept,
                'paying_guest'   => $booking->gross_amount,
                'total_cash'     => $cash + $oop,
                'price'          => 0,
                'amount'         => 0,
                'information'    => $information,
                'arrival_time'   => $booking->tour_date
            ];
        }

        return $calculated;
    }

    public function store()
    {
        $reports = $this->calculate();

        $savedReports = [];

        foreach ($reports as $reportData) {
            $report = DailyReport::updateOrCreate(
                ['salaries_id' => $reportData['salaries_id']],
                $reportData
            );
            $savedReports[] = $report;
        }

        return response()->json([
            'message' => 'Laporan berhasil disimpan.',
            'data'    => $savedReports,
        ]);
    }

}