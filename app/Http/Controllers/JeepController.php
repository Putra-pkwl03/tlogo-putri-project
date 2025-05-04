<?php

namespace App\Http\Controllers;

use App\Models\Jeep;
use Illuminate\Http\Request;

class JeepController extends Controller
{
    // CREATE
    public function create(Request $request)
    {
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'plat_jeep' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'tahun_kendaraan' => 'required|integer',
            'status' => 'required|string|max:255',
            'foto_jeep' => 'nullable|string|max:255',
        ]);

        $jeep = Jeep::create([
            'users_id' => $request->users_id,
            'plat_jeep' => $request->plat_jeep,
            'merek' => $request->merek,
            'tipe' => $request->tipe,
            'tahun_kendaraan' => $request->tahun_kendaraan,
            'status' => $request->status,
            'foto_jeep' => $request->foto_jeep ?? null,
        ]);

        return response()->json([
            'message' => 'Jeep berhasil ditambahkan!',
            'data' => $jeep
        ], 201);
    }

    // READ - Semua jeep
    public function index()
    {
        $jeeps = Jeep::all();

        return response()->json([
            'message' => 'Data jeep berhasil diambil!',
            'data' => $jeeps
        ], 200);
    }

    // READ - Berdasarkan ID
    public function showById($id)
    {
        $jeep = Jeep::find($id);

        if (!$jeep) {
            return response()->json([
                'message' => 'Jeep tidak ditemukan!'
            ], 404);
        }

        return response()->json([
            'message' => 'Data jeep berhasil diambil!',
            'data' => $jeep
        ], 200);
    }

    // READ - Berdasarkan STATUS
    public function showByStatus($status)
    {
        $jeeps = Jeep::where('status', $status)->get();

        if ($jeeps->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada jeep dengan status tersebut!'
            ], 404);
        }

        return response()->json([
            'message' => 'Data jeep berdasarkan status berhasil diambil!',
            'data' => $jeeps
        ], 200);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $jeep = Jeep::find($id);

        if (!$jeep) {
            return response()->json(['message' => 'Jeep tidak ditemukan!'], 404);
        }

        $jeep->update($request->only([
            'plat_jeep', 'merek', 'tipe', 'tahun_kendaraan', 'status', 'foto_jeep'
        ]));

        return response()->json([
            'message' => 'Jeep berhasil diupdate!',
            'data' => $jeep
        ], 200);
    }

    // DELETE
    public function delete($id)
    {
        $jeep = Jeep::find($id);

        if (!$jeep) {
            return response()->json(['message' => 'Jeep tidak ditemukan!'], 404);
        }

        $jeep->delete();

        return response()->json(['message' => 'Jeep berhasil dihapus!'], 200);
    }
}
