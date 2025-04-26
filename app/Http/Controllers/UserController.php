<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Add user
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:25|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Front Office,Owner,Driver,Pengurus',
        ];

        $roleFields = [
            'Front Office' => [],
            'Owner' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung', 'jumlah_jeep'],
            'Driver' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung', 'plat_jeep', 'foto_jeep'],
            'Pengurus' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung'],
        ];

        $role = $request->input('role');

        if (isset($roleFields[$role])) {
            foreach ($roleFields[$role] as $field) {
                $rules[$field] = in_array($field, ['foto_profil', 'foto_jeep'])
                    ? 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                    : 'required|string';
            }
        }

        $validated = $request->validate($rules);

        // Upload file jika ada
        if ($request->hasFile('foto_profil')) {
            $validated['foto_profil'] = $request->file('foto_profil')->store('profile_images', 'public');
        }

        if ($request->hasFile('foto_jeep')) {
            $validated['foto_jeep'] = $request->file('foto_jeep')->store('jeep_images', 'public');
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Generate Token
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    // Get all users (Hanya bisa diakses oleh FO yang login)
    public function all(Request $request)
    {
        $user = User::find(Auth::guard('fo')->id());

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token tidak valid atau tidak ada.'
            ], 401);
        }

        return response()->json(User::all());
    }

    // Get user (Hanya FO)
    public function me()
    {
        $user = Auth::guard('fo')->user(); // Ambil user dari token aktif

        // Cek role dan sesuaikan data yang ingin ditampilkan
        switch ($user->role) {
            case 'Front Office':
                $data = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ];
                break;

            case 'Owner':
                $data = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'telepon' => $user->telepon,
                    'alamat' => $user->alamat,
                    'foto_profil' => $user->foto_profil,
                    'jumlah_jeep' => $user->jumlah_jeep,
                    'tanggal_bergabung' => $user->tanggal_bergabung,
                    'status' => $user->status,
                    'role' => $user->role,
                ];
                break;

            case 'Driver':
                $data = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'telepon' => $user->telepon,
                    'alamat' => $user->alamat,
                    'foto_profil' => $user->foto_profil,
                    'plat_jeep' => $user->plat_jeep,
                    'foto_jeep' => $user->foto_jeep,
                    'tanggal_bergabung' => $user->tanggal_bergabung,
                    'status' => $user->status,
                    'role' => $user->role,
                ];
                break;

            case 'Pengurus':
                $data = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'telepon' => $user->telepon,
                    'alamat' => $user->alamat,
                    'foto_profil' => $user->foto_profil,
                    'tanggal_bergabung' => $user->tanggal_bergabung,
                    'status' => $user->status,
                    'role' => $user->role,
                ];
                break;
            default:
                $data = $user; // fallback: kirim semua data user
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Get users by role
    public function getUsersByRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        $role = strtoupper($request->role);

        $users = User::where('role', $role)->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada user dengan role tersebut.'
            ], 404);
        }
        // Map data sesuai role
        $data = $users->map(function ($user) use ($role) {
            switch ($role) {
                case 'Front Office':
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role' => $user->role,
                    ];

                case 'Driver':
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'telepon' => $user->telepon,
                        'alamat' => $user->alamat,
                        'foto_profil' => $user->foto_profil,
                        'plat_jeep' => $user->plat_jeep,
                        'foto_jeep' => $user->foto_jeep,
                        'tanggal_bergabung' => $user->tanggal_bergabung,
                        'status' => $user->status,
                        'role' => $user->role,
                    ];

                case 'Owner':
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'telepon' => $user->telepon,
                        'alamat' => $user->alamat,
                        'foto_profil' => $user->foto_profil,
                        'jumlah_jeep' => $user->jumlah_jeep,
                        'tanggal_bergabung' => $user->tanggal_bergabung,
                        'status' => $user->status,
                        'role' => $user->role,
                    ];

                case 'Pengurus':
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'telepon' => $user->telepon,
                        'alamat' => $user->alamat,
                        'foto_profil' => $user->foto_profil,
                        'jabatan' => $user->jabatan,
                        'tanggal_bergabung' => $user->tanggal_bergabung,
                        'status' => $user->status,
                        'role' => $user->role,
                    ];
                default:
                    return $user; // fallback: kirim semua data
            }
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Update User
    public function update(Request $request, $id = null)
    {
        $authUser = Auth::user();

        // Cek apakah FO atau bukan
        if ($authUser->role === 'Front Office') {
            // FO bisa update data siapa saja
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID user wajib disertakan untuk update oleh FO.'
                ], 400);
            }
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ], 404);
            }
        } else {
            // Selain FO, hanya bisa update dirinya sendiri
            $user = $authUser;
        }

        // Validasi input
        $request->validate([
            'name' => 'nullable|string',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'telepon' => 'nullable|string',
            'foto_profil' => 'nullable|file',
            'plat_jeep' => 'nullable|string',
            'foto_jeep' => 'nullable|file',
            'jumlah_jeep' => 'nullable|string',
            // 'status' validasi manual di bawah
        ]);

        // Data yang boleh diupdate semua role
        $dataToUpdate = $request->only([
            'name',
            'alamat',
            'email',
            'telepon',
            'plat_jeep',
            'jumlah_jeep',
        ]);

        // Kalau FO atau user yang update dirinya sendiri boleh update status
        if (($authUser->role === 'Front Office' || $authUser->id === $user->id) && $request->has('status')) {
            $dataToUpdate['status'] = $request->status;
        }

        // Handle upload foto_profil
        if ($request->hasFile('foto_profil')) {
            $dataToUpdate['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        // Handle upload foto_jeep
        if ($request->hasFile('foto_jeep')) {
            $dataToUpdate['foto_jeep'] = $request->file('foto_jeep')->store('foto_jeep', 'public');
        }

        $user->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui.',
            'data' => $user
        ]);
    }


    // Delete user
    public function delete($id)
    {
        $authUser = Auth::user();

        // Cek apakah user yang login adalah FO
        if (!$authUser || $authUser->role !== 'Front Office') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya FO yang dapat menghapus user.'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.'
        ]);
    }
}
