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
        // Ambil bulan dan tahun dari request atau default ke bulan dan tahun sekarang
        $bulan = $request->query('bulan', Carbon::now()->format('m'));
        $tahun = $request->query('tahun', Carbon::now()->format('Y'));

        // Ambil data berdasarkan bulan dan tahun
        $rekapreport = Report::whereMonth('report_date', $bulan)
                             ->whereYear('report_date', $tahun)
                             ->get();

        // Jika data kosong, kirimkan pesan bahwa data tidak ditemukan
        if ($rekapreport->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Data tidak ditemukan untuk bulan dan tahun yang diminta.',
                'data' => []
            ], 404);
        }

        // Jika data ditemukan, kirim data
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditemukan.',
            'data' => $rekapreport
        ]);
    }


    public function rekapMingguan(Request $request)
    {
        $quarter = $request->input('quarter');
        $year = $request->input('year');

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

        // Subquery net_cash terakhir per minggu
        $latestNetCash = DB::table('report')
            ->select(
                DB::raw('YEAR(report_date) as tahun'),
                DB::raw('MONTH(report_date) as bulan'),
                DB::raw('WEEK(report_date, 1) - WEEK(DATE_FORMAT(report_date, "%Y-%m-01"), 1) + 1 as minggu_ke'),
                'net_cash',
                'report_date'
            )
            ->whereBetween('report_date', [$startDate, $endDate])
            ->orderBy('report_date', 'desc');

        $latestPerWeek = DB::table(DB::raw("({$latestNetCash->toSql()}) as sub"))
            ->mergeBindings($latestNetCash)
            ->groupBy('tahun', 'bulan', 'minggu_ke')
            ->select(
                'tahun',
                'bulan',
                'minggu_ke',
                DB::raw('MAX(net_cash) as net_cash_terakhir')
            );

        // Agregasi mingguan
        $data = DB::table('report')
            ->whereBetween('report_date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(report_date) as tahun'),
                DB::raw('MONTH(report_date) as bulan'),
                DB::raw('WEEK(report_date, 1) - WEEK(DATE_FORMAT(report_date, "%Y-%m-01"), 1) + 1 as minggu_ke'),
                DB::raw('SUM(cash) as total_cash'),
                DB::raw('SUM(operational) as total_operational'),
                DB::raw('SUM(expenditure) as total_expenditure'),
                DB::raw('SUM(clean_operations) as total_clean_operations'),
                DB::raw('SUM(jeep_amount) as total_jeep_amount')
            )
            ->groupBy('tahun', 'bulan', 'minggu_ke');

        // Gabungkan dan ambil hasil
        $final = DB::table(DB::raw("({$data->toSql()}) as summary"))
            ->mergeBindings($data)
            ->leftJoinSub($latestPerWeek, 'latest', function ($join) {
                $join->on('summary.tahun', '=', 'latest.tahun')
                     ->on('summary.bulan', '=', 'latest.bulan')
                     ->on('summary.minggu_ke', '=', 'latest.minggu_ke');
            })
            ->select(
                'summary.tahun',
                'summary.bulan',
                'summary.minggu_ke',
                'summary.total_cash',
                'summary.total_operational',
                'summary.total_expenditure',
                'summary.total_clean_operations',
                'summary.total_jeep_amount',
                'latest.net_cash_terakhir as net_cash'
            )
            ->orderBy('summary.tahun')
            ->orderBy('summary.bulan')
            ->orderBy('summary.minggu_ke')
            ->get()
            ->map(function ($item) {
                $item->minggu = 'Minggu ' . $item->minggu_ke;
                unset($item->minggu_ke);
                return $item;
            });

        // Cek jika data kosong
        if ($final->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Data tidak ditemukan untuk kuartal dan tahun yang diminta.',
                'data' => []
            ], 404);
        }

        // Jika data ada
        return response()->json([
            'status' => 'success',
            'message' => 'Data mingguan berhasil ditemukan.',
            'data' => $final
        ]);
    }



    public function rekapPerBulan(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
    
        // Subquery untuk net_cash terakhir tiap bulan
        $latestNetCash = DB::table('report')
            ->select(
                DB::raw('YEAR(report_date) as tahun'),
                DB::raw('MONTH(report_date) as bulan'),
                'net_cash',
                'report_date'
            )
            ->whereYear('report_date', $tahun)
            ->orderBy('report_date', 'desc');
            
        $netCashPerBulan = DB::table(DB::raw("({$latestNetCash->toSql()}) as sub"))
            ->mergeBindings($latestNetCash)
            ->groupBy('tahun', 'bulan')
            ->select(
                'tahun',
                'bulan',
                DB::raw('MAX(net_cash) as net_cash_terakhir')
            );
        
        // Agregasi data lainnya
        $summary = DB::table('report')
            ->select(
                DB::raw('YEAR(report_date) as tahun'),
                DB::raw('MONTH(report_date) as bulan'),
                DB::raw('SUM(cash) as total_cash'),
                DB::raw('SUM(operational) as total_operational'),
                DB::raw('SUM(expenditure) as total_expenditure'),
                DB::raw('SUM(clean_operations) as total_clean_operations'),
                DB::raw('SUM(jeep_amount) as total_jeep_amount')
            )
            ->whereYear('report_date', $tahun)
            ->groupBy(DB::raw('YEAR(report_date)'), DB::raw('MONTH(report_date)'));
            
        // Gabungkan dengan net_cash terakhir
        $data = DB::table(DB::raw("({$summary->toSql()}) as summary"))
            ->mergeBindings($summary)
            ->leftJoinSub($netCashPerBulan, 'net', function ($join) {
                $join->on('summary.tahun', '=', 'net.tahun')
                     ->on('summary.bulan', '=', 'net.bulan');
            })
            ->select(
                'summary.tahun',
                'summary.bulan',
                'summary.total_cash',
                'summary.total_operational',
                'summary.total_expenditure',
                'summary.total_clean_operations',
                'summary.total_jeep_amount',
                'net.net_cash_terakhir as net_cash'
            )
            ->orderBy('summary.tahun', 'desc')
            ->orderBy('summary.bulan', 'desc')
            ->get();
            
        if ($data->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Data tidak ditemukan untuk tahun yang diminta.',
                'data' => []
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Data rekap per bulan ditemukan.',
            'data' => $data
        ]);
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
