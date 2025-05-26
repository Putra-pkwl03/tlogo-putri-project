<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyReport;
use App\Models\IncomeReport;
use App\Models\ExpenditureReport;
use App\Models\Salaries;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        {
            $incomereport = IncomeReport::all();
            return response()->json($incomereport);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dailys = DailyReport::all();
         $incomeReports = [];
     
         foreach ($dailys as $daily) {
            $expenditure = ExpenditureReport::where('salaries_id', $daily->salaries_id)->first();
            $salary = Salaries::where('salaries_id', $daily->salaries_id)->first();
        
            if (!$expenditure || !$salary) {
                continue;
            }
        
            // Cek unik ticketing_id
            $exists = IncomeReport::where('ticketing_id', $salary->ticketing_id)->exists();
            if ($exists) {
                continue; // Skip jika sudah ada record dengan ticketing_id sama
            }
        
            $incomeReports[] = IncomeReport::create([
                'booking_id' => $daily->booking_id,
                'ticketing_id' => $salary->ticketing_id,
                'expenditure_id' => $expenditure->expenditure_id,
                'booking_date' => $daily->arrival_time,
                'income' => $daily->paying_guest,
                'expediture' => $daily->driver_accept,
                'cash' => $daily->total_cash,
            ]);
        }
        
     
         return response()->json([
             'message' => 'Laporan berhasil dibuat.',
             'data'    => $incomeReports
         ]);
    }
}
