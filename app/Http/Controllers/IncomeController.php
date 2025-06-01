<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyReport;
use App\Models\IncomeReport;
use App\Models\ExpenditureReport;
use App\Models\Salary;

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
    public function calculate()
    {
        $dailys = DailyReport::all();
        $incomereport = [];
    
        foreach ($dailys as $daily) {
            \Log::info('Checking daily report', ['salaries_id' => $daily->salaries_id]);
        
            $expenditure = ExpenditureReport::where('salaries_id', $daily->salaries_id)->first();
            $salary = Salary::where('salaries_id', $daily->salaries_id)->first();
        
            if (!$expenditure) {
                \Log::info('Expenditure not found', ['salaries_id' => $daily->salaries_id]);
            }
            if (!$salary) {
                \Log::info('Salary not found', ['salaries_id' => $daily->salaries_id]);
            }
        
            if (!$expenditure || !$salary) {
                continue;
            }
        
            $incomereport[] = [
                'booking_id' => $daily->booking_id,
                'ticketing_id' => $salary->ticketing_id,
                'expenditure_id' => $expenditure->expenditure_id,
                'booking_date' => $daily->arrival_time,
                'income' => $daily->paying_guest,
                'expediture' => $daily->driver_accept,
                'cash' => $daily->total_cash,
            ];
        }
    
        return $incomereport;
    }

        
    public function store()
    {   
        $incomereport = $this->calculate();

        \Log::info('Income Report Data:', $incomereport); // <--- Tambah ini

        $savedReports = [];
        foreach ($incomereport as $daily) {
            if (!IncomeReport::where('ticketing_id', $daily['ticketing_id'])->exists()) {
                $savedReports[] = IncomeReport::create($daily);
            }
        }

        return response()->json([
            'message' => 'Laporan berhasil dibuat.',
            'data'    => $savedReports
        ]);
    }
}
