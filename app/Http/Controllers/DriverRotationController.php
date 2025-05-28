<?php

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
        if (DriverRotation::where('date', $besok)->exists()) {
            return response()->json(['message' => 'Rotasi untuk besok sudah dibuat.'], 400);
        }

        $drivers = User::where('role', 'driver')
            ->orderBy('last_assigned_at')
            ->get();

        foreach ($drivers as $driver) {
            DriverRotation::create([
                'date' => $besok,
                'driver_id' => $driver->id,
                'assigned' => false, // default
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

    // Tampilkan rotasi untuk tanggal tertentu (default hari ini)
    public function index(Request $request)
    {
        $tanggal = $request->date ?? Carbon::today()->toDateString();

        $rotasi = DriverRotation::with('driver')
            ->where('date', $tanggal)
            ->orderBy('id')
            ->get();

        return response()->json($rotasi);
    }

    public function assign($rotationId)
    {
        $rotation = DriverRotation::with('driver')->findOrFail($rotationId);

        $rotation->update([
            'assigned' => true,
            'skip_reason' => null, 
        ]);

        $rotation->driver->update([
            'last_assigned_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Driver berhasil ditugaskan kembali.']);
    }
    
}