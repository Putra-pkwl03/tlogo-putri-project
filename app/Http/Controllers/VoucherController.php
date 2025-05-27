<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(){
        $vouchers = Voucher::all();
        return response()->json($vouchers, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:vouchers',
            'discount' => 'required|numeric',
        ]);
    
        $voucher = Voucher::create($validated);
    
        return response()->json([
            'message' => 'Voucher created',
            'data' => $voucher,
        ], 201);
    }

    public function show($id){
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        return response()->json($voucher);
    }
    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);
    
        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }
    
        $request->validate([
            'code' => 'required|unique:vouchers,code,' . $voucher->id,
            'discount' => 'required|numeric',
        ]);
    
        $voucher->update([
            'code' => $request->code,
            'discount' => $request->discount,
        ]);
    
        return response()->json([
            'message' => 'Voucher updated',
            'data' => $voucher
        ], 200);
    }

    public function destroy($id){
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        $voucher->delete();
        return response()->json(['message' => 'Voucher deleted'], 200);
    }
}
