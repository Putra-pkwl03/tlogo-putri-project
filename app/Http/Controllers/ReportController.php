<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\ExpenditureReport;
use App\Models\DailyReport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari query string jika ada
        $bulan = $request->query('bulan', Carbon::now()->month); // Default: bulan ini
        $tahun = $request->query('tahun', Carbon::now()->year);  // Default: tahun ini

        // Filter berdasarkan bulan dan tahun
        $rekapreport = Report::whereMonth('report_date', $bulan)
                             ->whereYear('report_date', $tahun)
                             ->get();

        return response()->json($rekapreport);
    }

    public function rekapMingguan(Request $request)
    {
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
    
        $data = DB::table('report')
            ->whereBetween('report_date', [$startDate, $endDate])
            ->select(
                DB::raw('WEEK(report_date, 1) - WEEK(DATE_FORMAT(report_date, "%Y-%m-01"), 1) + 1 as minggu_ke'),
                DB::raw('YEAR(report_date) as tahun'),
                DB::raw('MONTH(report_date) as bulan'),
                DB::raw('SUM(cash) as total_cash'),
                DB::raw('SUM(operational) as total_operational'),
                DB::raw('SUM(expenditure) as total_expenditure'),
                DB::raw('SUM(net_cash) as total_net_cash'),
                DB::raw('SUM(clean_operations) as total_clean_operations'),
                DB::raw('SUM(jeep_amount) as total_jeep_amount'),
            )
            ->groupBy('tahun', 'bulan', 'minggu_ke')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('minggu_ke')
            ->get()
            ->map(function ($item) {
                $item->minggu = 'Minggu ' . $item->minggu_ke;
                unset($item->minggu_ke);
                return $item;
            });
        
        return response()->json($data);
    }

    public function rekapPerBulan(Request $request)
    {
        // Jika ada request tahun, gunakan itu. Kalau tidak, pakai tahun ini
        $tahun = $request->input('tahun', date('Y'));

        $data = DB::table('report')
            ->select(
                DB::raw('YEAR(report_date) as tahun'),
                DB::raw('MONTH(report_date) as bulan'),
                DB::raw('SUM(cash) as total_cash'),
                DB::raw('SUM(operational) as total_operational'),
                DB::raw('SUM(expenditure) as total_expenditure'),
                DB::raw('SUM(net_cash) as total_net_cash'),
                DB::raw('SUM(clean_operations) as total_clean_operations'),
                DB::raw('SUM(jeep_amount) as total_jeep_amount'),
            )
            ->whereYear('report_date', $tahun)
            ->groupBy(DB::raw('YEAR(report_date)'), DB::raw('MONTH(report_date)'))
            ->orderBy(DB::raw('YEAR(report_date)'), 'desc')
            ->orderBy(DB::raw('MONTH(report_date)'), 'desc')
            ->get();

        return response()->json($data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function calculatereport()
    {
        $tanggalList = DB::table('expenditure_report')
            ->select('issue_date as tanggal')
            ->union(
                DB::table('daily_reports')->select('arrival_time as tanggal')
            )
            ->distinct()
            ->pluck('tanggal');
            
        $rekapreport = [];
            
        foreach ($tanggalList as $tanggal) {
            // Total pengeluaran (tidak termasuk gaji driver & gaji owner)
            $totalPengeluaran = DB::table('expenditure_report')
                ->where('issue_date', $tanggal)
                ->whereRaw("LOWER(information) NOT LIKE ?", ['%gaji driver%'])
                ->whereRaw("LOWER(information) NOT LIKE ?", ['%gaji owner%'])
                ->sum('amount');
        
            // Total pemasukan
            $totalKas = DB::table('daily_reports')
                ->where('arrival_time', $tanggal)
                ->sum('cash');
        
            $totalOpp = DB::table('daily_reports')
                ->where('arrival_time', $tanggal)
                ->sum('oop');
        
            // Jumlah keterangan (masih hitung semua entri, termasuk gaji)
            $jumlahjeep = DB::table('daily_reports')
                ->where('arrival_time', $tanggal)
                ->count('id_daily_report');
        
            $oppBersih = $totalOpp - $totalPengeluaran;
        
            $kasbersih = $totalKas + $oppBersih;
        
            // Update or create report berdasarkan report_date unik
            $report = Report::updateOrCreate(
                ['report_date' => $tanggal], // kondisi unik
                [
                    'cash' => $totalKas,
                    'operational' => $totalOpp,
                    'expenditure' => $totalPengeluaran,
                    'net_cash' => $kasbersih,
                    'clean_operations' => $oppBersih,
                    'jeep_amount' => $jumlahjeep,
                ]
            );
        
            $rekapreport[] = $report;
        }
    
        return response()->json([
            'message' => 'Rekap berhasil disimpan.',
            'data' => $rekapreport,
        ]);
    }

    
}
