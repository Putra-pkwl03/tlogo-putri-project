<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
            'Driver' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung'],
            'Pengurus' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung'],
        ];

        $role = $request->input('role');

        if (isset($roleFields[$role])) {
            foreach ($roleFields[$role] as $field) {
                $rules[$field] = in_array($field, ['foto_profil'])
                    ? 'nullable|image|mimes:jpeg,png,jpg|max:3072'
                    : 'required|string';
            }
        }

        $validated = $request->validate($rules);

        // Upload file jika ada
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('profile_images', 'public');
            $validated['foto_profil'] = $path;
            $validated['foto_profil_url'] = Storage::url($path); // Tambah URL ke response
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan.',
            'data' => [
                'user' => $user,
                'foto_profil_url' => $user->foto_profil ? Storage::url($user->foto_profil) : null,
            ]
        ], 201);
    }

    // Get all users (Hanya bisa diakses oleh FO yang login)
    public function all(Request $request)
    {
        $user = Auth::guard('fo')->user();

        if (!$user || $user->role !== 'Front Office') {
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
                    'tanggal_bergabung' => $user->tanggal_bergabung,
                    'status' => $user->status,
                    'role' => $user->role,
                    'konfirmasi' => $user->konfirmasi
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
                        'tanggal_bergabung' => $user->tanggal_bergabung,
                        'status' => $user->status,
                        'role' => $user->role,
                        'konfirmasi' => $user->konfirmasi
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

        // Cek apakah Front Office atau bukan
        if ($authUser->role === 'Front Office') {
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
            // Pengguna biasa hanya bisa update dirinya sendiri
            $user = $authUser;
            if ($id && $authUser->id != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengupdate data pengguna lain.'
                ], 403);
            }

            // Batasi field yang boleh dikirim oleh non-FO
            $allowedFields = ['name', 'username', 'email', 'password', 'telepon', 'alamat', 'foto_profil'];
            $unexpectedFields = array_diff(array_keys($request->all()), $allowedFields);

            if (!empty($unexpectedFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengubah field: ' . implode(', ', $unexpectedFields)
                ], 403);
            }
        }

        // Aturan validasi
        $rules = [
            'name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:25|unique:users,username,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|unique:users,telepon,' . $user->id,
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'konfirmasi' => 'nullable|in:Bisa,Tidak Bisa'
        ];

        if ($authUser->role === 'Front Office') {
            $rules += [
                'role' => 'nullable|in:Front Office,Owner,Driver,Pengurus',
                'status' => 'nullable|string',
                'jumlah_jeep' => 'nullable|integer|min:0',
            ];
        }

        // Validasi input
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Siapkan data yang akan diupdate
        $dataToUpdate = $request->only([
            'name',
            'username',
            'email',
            'alamat',
            'telepon',
        ]);

        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        if ($authUser->role === 'Front Office') {
            $dataToUpdate += $request->only([
                'role',
                'status',
                'jumlah_jeep',
                'konfirmasi'
            ]);
        }

        if ($request->hasFile('foto_profil')) {
            $dataToUpdate['foto_profil'] = $request->file('foto_profil')->store('foto_profil', 'public');
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
