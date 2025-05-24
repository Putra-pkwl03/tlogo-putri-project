<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenditureReport;
use App\Models\Salaries;

class ExpeditureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenditureReport = ExpenditureReport::all();
        return response()->json([
            'salaries' => $expenditureReport,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi data yang dikirim dari frontend
        $validatedData = $request->validate([
            'salaries_id' => 'nullable|exists:salaries,id', // salaries_id tidak wajib
            'issue_date' => 'required|date',
            'amount' => 'required|numeric',
            'information' => 'required|string',
            'action' => 'required|string',
        ]);

        // Simpan data ke dalam tabel ExpenditureReport
        $expenditureReport = ExpenditureReport::create([
            'salaries_id' => $validatedData['salaries_id'] ?? null, // Set null jika tidak ada salaries_id
            'issue_date' => $validatedData['issue_date'],
            'amount' => $validatedData['amount'],
            'information' => $validatedData['information'],
            'action' => $validatedData['action'],
        ]);

        // Kembalikan respon JSON
        return response()->json([
            'message' => 'Data berhasil disimpan.',
            'data' => $expenditureReport,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */    

     public function storeformsalarie()
     {
         $salaries = Salaries::all();
         $expenditureReports = [];
     
         foreach ($salaries as $salary) {
             $expenditureReports[] = ExpenditureReport::create([
                 'salaries_id'  => $salary->salaries_id,
                 'issue_date'   => $salary->payment_date,
                 'amount'       => $salary->total_salary,
                 'information'  => 'gaji ' . $salary->role . ' ' . $salary->nama,
                 'action'       => 'menambah gaji ' . $salary->role,
             ]);
         }
     
         return response()->json([
             'message' => 'Laporan berhasil dibuat.',
             'data'    => $expenditureReports
         ]);
     }
     
    public function update(Request $request, string $expenditure_id)
    {
        // Validasi data yang dikirim dari frontend
        $validatedData = $request->validate([
            'salaries_id' => 'nullable|exists:salaries,id', // salaries_id tidak wajib
            'issue_date' => 'required|date',
            'amount' => 'required|numeric',
            'information' => 'required|string',
            'action' => 'required|string',
        ]);

        // Cari data ExpenditureReport berdasarkan ID
        $expenditureReport = ExpenditureReport::findOrFail($expenditure_id);

        // Update data ExpenditureReport
        $expenditureReport->update([
            'salaries_id' => $validatedData['salaries_id'] ?? null, // Set null jika tidak ada salaries_id
            'issue_date' => $validatedData['issue_date'],
            'amount' => $validatedData['amount'],
            'information' => $validatedData['information'],
            'action' => $validatedData['action'],
        ]);

        // Kembalikan respon JSON
        return response()->json([
            'message' => 'Data berhasil diperbarui.',
            'data' => $expenditureReport,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $expenditure_id)
    {
        // Cari data ExpenditureReport berdasarkan ID
        $expenditureReport = ExpenditureReport::findOrFail($expenditure_id);

        // Hapus data ExpenditureReport
        $expenditureReport->delete();

        // Kembalikan respon JSON
        return response()->json([
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
