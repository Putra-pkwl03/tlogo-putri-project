<?php

namespace App\Http\Controllers;

use App\Models\HistoryTicketing;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class HistoryTicketingController extends Controller
{
    // Menampilkan semua data histori ticketing
    public function index()
    {
        try {
            $histories = HistoryTicketing::with(['ticketing', 'changedBy', 'driver', 'jeep', 'booking'])->latest()->get();
            return response()->json($histories);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data histori', 'error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan histori berdasarkan ID
    public function show($id)
    {
        try {
            $history = HistoryTicketing::with(['ticketing', 'changedBy', 'driver', 'jeep', 'booking'])->findOrFail($id);
            return response()->json($history);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Histori tidak ditemukan'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal menampilkan histori', 'error' => $e->getMessage()], 500);
        }
    }

    // Menampilkan histori berdasarkan driver_id
    public function historyByDriver($driver_id)
    {
        try {
            $histories = HistoryTicketing::with(['ticketing', 'changedBy', 'jeep', 'booking'])
                ->where('driver_id', $driver_id)
                ->orderByDesc('created_at')
                ->get();

            if ($histories->isEmpty()) {
                return response()->json(['message' => 'Tidak ada histori untuk driver ini'], 404);
            }

            return response()->json($histories);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal mengambil histori driver', 'error' => $e->getMessage()], 500);
        }
    }
}