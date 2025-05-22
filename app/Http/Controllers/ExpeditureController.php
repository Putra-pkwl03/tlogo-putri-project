<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenditureReport;
use App\Models\Salaries;
use App\Models\ExpenditureAll;

class ExpeditureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expendituresalaries = ExpenditureReport::with(['salaries'])->get();
        $expenditureall = ExpenditureAll::all(); // Fetch all records without relationships
        return response()->json([
            'salaries' => $expendituresalaries,
            'all' => $expenditureall,
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
        $expenditureAll = ExpenditureAll::create([
            'salaries_id' => $validatedData['salaries_id'] ?? null, // Set null jika tidak ada salaries_id
            'issue_date' => $validatedData['issue_date'],
            'amount' => $validatedData['amount'],
            'information' => $validatedData['information'],
            'action' => $validatedData['action'],
        ]);

        // Kembalikan respon JSON
        return response()->json([
            'message' => 'Data berhasil disimpan.',
            'data' => $expenditureAll,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */    

    public function storeformsalarie($salaries_id)
    {
        $salaries = Salaries::findOrFail($salaries_id);

        $expenditureReport=ExpenditureReport::create([
            'salaries_id'=> $salaries->id,
            'issue_date'=> $salaries->payment_date,
            'amount' => $salaries->total_salary,
            'information'=> 'gaji' . $salaries->role . ' ' . $salaries->nama,
            'action' =>'menambah gaji' . $salaries->role,
        ]);

        return response()->json(['message' => 'Laporan berhasil dibuat.', data => $expenditureReport]);
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
