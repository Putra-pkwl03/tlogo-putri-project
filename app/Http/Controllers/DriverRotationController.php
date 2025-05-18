<?php

// app/Http/Controllers/DriverRotationController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DriverRotation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DriverRotationController extends Controller
{
    // Buat rotasi untuk besok
    public function generate()
    {
        $besok = Carbon::tomorrow()->toDateString();

        // Cek apakah sudah dibuat sebelumnya
        if (DriverRotation::where('date', $besok)->exists()) {
            return response()->json(['message' => 'Rotasi untuk besok sudah dibuat.'], 400);
        }

        // Ambil semua driver
        $drivers = User::where('role', 'driver')->orderBy('last_assigned_at')->get();

        foreach ($drivers as $driver) {
            DriverRotation::create([
                'date' => $besok,
                'driver_id' => $driver->id,
            ]);
        }

        return response()->json(['message' => 'Rotasi besok berhasil dibuat.']);
    }

    // Tandai driver berhalangan
    public function skip(Request $request, $rotationId)
    {
        $rotation = DriverRotation::findOrFail($rotationId);
        $rotation->update([
            'assigned' => false,
            'skip_reason' => $request->skip_reason ?? 'Berhalangan',
        ]);

        return response()->json(['message' => 'Driver ditandai berhalangan.']);
    }

    // Tampilkan semua rotasi untuk tanggal tertentu
    public function index(Request $request)
    {
        $tanggal = $request->date ?? Carbon::today()->toDateString();

        $rotasi = DriverRotation::with('driver')
            ->where('date', $tanggal)
            ->orderBy('id')
            ->get();

        return response()->json($rotasi);
    }
}
