<?php

namespace App\Http\Controllers;

use App\Models\Jeep;
use App\Models\User;
use Illuminate\Http\Request;

class JeepController extends Controller
{
    // CREATE
    public function create(Request $request)
    {
        $request->validate([
            'users_id' => 'nullable|exists:users,id',
            'owner_id' => 'required|exists:users,id',
            'driver_id' => 'required|exists:users,id',
            'no_lambung' => 'required|string|max:255',
            'plat_jeep' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'tahun_kendaraan' => 'required|integer',
            'status' => 'required|string|max:255',
            'foto_jeep' => 'nullable|string|max:255',
        ]);

        // Cek apakah owner valid dan berperan sebagai Owner
        $owner = User::find($request->owner_id);

        if (!$owner || $owner->role !== 'Owner') {
            return response()->json([
                'message' => 'User bukan owner atau tidak ditemukan.'
            ], 400);
        }

        // Cek jumlah jeep yang dimiliki owner
        $jumlahJeep = Jeep::where('owner_id', $request->owner_id)->count();

        if ($jumlahJeep >= 2) {
            return response()->json([
                'message' => 'Owner ini sudah memiliki maksimal 2 jeep.'
            ], 400);
        }

        // Simpan jeep
        $jeep = Jeep::create([
            'users_id' => $request->users_id,
            'owner_id' => $request->owner_id,
            'driver_id' => $request->driver_id,
            'no_lambung' => $request->no_lambung,
            'plat_jeep' => $request->plat_jeep,
            'merek' => $request->merek,
            'tipe' => $request->tipe,
            'tahun_kendaraan' => $request->tahun_kendaraan,
            'status' => $request->status,
            'foto_jeep' => $request->foto_jeep ?? null,
        ]);

        // Tambahkan jumlah_jeep ke owner
        $owner->increment('jumlah_jeep');

        return response()->json([
            'message' => 'Jeep berhasil ditambahkan!',
            'data' => $jeep
        ], 201);
    }


    // READ - Semua jeep + relasi owner dan driver
    public function index()
    {
        $jeeps = Jeep::with(['owner', 'driver'])->get();

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

    // READ - Berdasarkan DRIVER
    // Filter berdasarkan Owner
    public function showByOwner($ownerId)
    {
        $jeeps = Jeep::where('owner_id', $ownerId)->with(['owner', 'driver'])->get();

        if ($jeeps->isEmpty()) {
            return response()->json(['message' => 'Tidak ada jeep milik owner ini!'], 404);
        }

        return response()->json([
            'message' => 'Data jeep berdasarkan owner berhasil diambil!',
            'data' => $jeeps
        ], 200);
    }

    // Filter berdasarkan Driver
    public function showByDriver($driverId)
    {
        $jeeps = Jeep::where('driver_id', $driverId)->with(['owner', 'driver'])->get();

        if ($jeeps->isEmpty()) {
            return response()->json(['message' => 'Tidak ada jeep milik driver ini!'], 404);
        }

        return response()->json([
            'message' => 'Data jeep berdasarkan driver berhasil diambil!',
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
            'no_lambung',
            'plat_jeep',
            'merek',
            'tipe',
            'tahun_kendaraan',
            'status',
            'foto_jeep'
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
            return response()->json(['message' => 'Jeep tidak ditemukan.'], 404);
        }

        $owner = User::find($jeep->owner_id);

        $jeep->delete();

        // Kurangi jumlah_jeep
        if ($owner && $owner->role === 'Owner' && $owner->jumlah_jeep > 0) {
            $owner->decrement('jumlah_jeep');
        }

        return response()->json(['message' => 'Jeep berhasil dihapus.']);
    }
}
