<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\ExpenditureReport;
use App\Models\DailyReport;
use App\Models\IncomeReport;

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
        $quarter = $request->input('quarter'); // 1, 2, 3, atau 4
        $year = $request->input('year');
    
        // Validasi quarter dan year
        if (!in_array($quarter, [1, 2, 3, 4])) {
            return response()->json(['error' => 'Invalid quarter'], 400);
        }
        if (!$year || !is_numeric($year) || $year < 1900 || $year > 2100) {
            $year = Carbon::now()->year;
        }
    
        $quarters = [
            1 => ['start' => 1, 'end' => 3],
            2 => ['start' => 4, 'end' => 6],
            3 => ['start' => 7, 'end' => 9],
            4 => ['start' => 10, 'end' => 12],
        ];
    
        $startMonth = $quarters[$quarter]['start'];
        $endMonth = $quarters[$quarter]['end'];
    
        $startDate = Carbon::create($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::create($year, $endMonth, 1)->endOfMonth()->endOfDay();
    
        // Debugging: cek tanggal start dan end
        // \Log::info("Start date: $startDate, End date: $endDate");
    
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
                DB::raw('SUM(jeep_amount) as total_jeep_amount')
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

    public function statistik()
    {
        // 1. Pemasukan per bulan
        $pemasukan = DB::table('income_report')
            ->select(DB::raw("DATE_FORMAT(booking_date, '%Y-%m') as bulan"), DB::raw("SUM(income) as total_pemasukan"))
            ->groupBy('bulan');

        // 2. Pengeluaran per bulan
        $pengeluaran = DB::table('expenditure_report')
            ->select(DB::raw("DATE_FORMAT(issue_date, '%Y-%m') as bulan"), DB::raw("SUM(amount) as total_pengeluaran"))
            ->groupBy('bulan');

        // 3. Kas bersih terakhir per bulan dari laporan
        $laporan_sub = DB::table('report')
            ->select(DB::raw("MAX(report_date) as tanggal_terakhir"))
            ->groupBy(DB::raw("DATE_FORMAT(report_date, '%Y-%m')"));

        $laporan = DB::table('report')
            ->joinSub($laporan_sub, 'terakhir', function ($join) {
                $join->on('report.report_date', '=', 'terakhir.tanggal_terakhir');
            })
            ->select(DB::raw("DATE_FORMAT(report.report_date, '%Y-%m') as bulan"), 'net_cash');

        // Gabungkan semua data berdasarkan bulan
        $data = DB::table(DB::raw("({$pemasukan->toSql()}) as pemasukan"))
            ->mergeBindings($pemasukan)
            ->leftJoinSub($pengeluaran, 'pengeluaran', 'pemasukan.bulan', '=', 'pengeluaran.bulan')
            ->leftJoinSub($laporan, 'laporan', 'pemasukan.bulan', '=', 'laporan.bulan')
            ->select(
                'pemasukan.bulan',
                'total_pemasukan',
                'total_pengeluaran',
                'net_cash'
            )
            ->orderBy('pemasukan.bulan', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function calculateOnly()
    {
        $tanggalList = DB::table('expenditure_report')
            ->select('issue_date as tanggal')
            ->union(
                DB::table('daily_reports')->select('arrival_time as tanggal')
            )
            ->distinct()
            ->orderBy('tanggal') // pastikan urut berdasarkan tanggal
            ->pluck('tanggal');

        $rekapreport = [];
        $kasbersihSebelumnya = 0;

        foreach ($tanggalList as $tanggal) {
            $totalPengeluaran = DB::table('expenditure_report')
                ->whereDate('issue_date', $tanggal)
                ->whereRaw("LOWER(information) NOT LIKE ?", ['%gaji driver%'])
                ->whereRaw("LOWER(information) NOT LIKE ?", ['%gaji owner%'])
                ->sum('amount');

            $totalKas = DB::table('daily_reports')
                ->whereDate('arrival_time', $tanggal)
                ->sum('cash');

            $totalOpp = DB::table('daily_reports')
                ->whereDate('arrival_time', $tanggal)
                ->sum('oop');

            $jumlahjeep = DB::table('daily_reports')
                ->whereDate('arrival_time', $tanggal)
                ->count('id_daily_report');

            $oppBersih = $totalOpp - $totalPengeluaran;
            $kasbersihHariIni = $totalKas + $oppBersih;
            $kasbersihAkumulasi = $kasbersihSebelumnya + $kasbersihHariIni;

            $rekapreport[] = [
                'report_date' => $tanggal,
                'cash' => $totalKas,
                'operational' => $totalOpp,
                'expenditure' => $totalPengeluaran,
                'net_cash' => $kasbersihAkumulasi,
                'clean_operations' => $oppBersih,
                'jeep_amount' => $jumlahjeep,
            ];

            $kasbersihSebelumnya = $kasbersihAkumulasi;
        }

        return $rekapreport;
    }


    public function generateAndStore()
    {
        // Panggil method kalkulasi
        $rekapreport = $this->calculateOnly();

        $savedReports = [];
        foreach ($rekapreport as $data) {
            if (!Report::where('report_date', $data['report_date'])->exists()) {
                $savedReports[] = Report::updateOrCreate($data);
            }
        }

        return response()->json([
            'message' => 'Laporan berhasil dikalkulasi dan disimpan.',
            'data' => $savedReports
        ]);
    }    
}
