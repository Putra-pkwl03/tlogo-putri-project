<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Tampilkan semua voucher dengan potongan 10, 15, atau 20.
     */
    public function index()
    {
        $validPotongan = [10, 15, 20];

        $vouchers = Voucher::whereIn('potongan', $validPotongan)->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar voucher dengan potongan 10, 15, atau 20',
            'data' => $vouchers
        ]);
    }
}