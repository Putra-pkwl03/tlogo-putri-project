<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Login FO
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('fo')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal. Email atau password salah.'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string',
            'foto_profil' => 'nullable|file',
            'status' => 'nullable|string',
            'tanggal_bergabung' => 'nullable|string',
            'plat_jeep' => 'nullable|string',
            'foto_jeep' => 'nullable|file',
            'jumlah_jeep' => 'nullable|string',
            'jabatan' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'foto_profil' => $request->foto_profil,
            'status' => $request->status,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'plat_jeep' => $request->plat_jeep,
            'foto_jeep' => $request->foto_jeep,
            'jumlah_jeep' => $request->jumlah_jeep,
            'jabatan' => $request->jabatan,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'));
    }

    // Ambil data FO yang login
    public function profile()
    {
        $user = Auth::guard('fo')->user();
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil, token dihapus.'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal, token tidak valid.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Refresh token
    public function refresh()
    {
        $token = JWTAuth::refresh();
        return $this->respondWithToken($token);
    }

    // Helper untuk return token JWT
    protected function respondWithToken($token)
    {
        $user = Auth::guard('fo')->user();
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'role' => $user->role
        ]);
    }
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:FO,OWNER,DRIVER,PENGURUS,BENDAHARA',
        ];

        $roleFields = [
            'FO' => [],
            'OWNER' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung', 'jumlah_jeep'],
            'DRIVER' => ['alamat', 'telepon', 'foto_profil', 'status', 'tanggal_bergabung', 'plat_jeep', 'foto_jeep'],
            'PENGURUS' => ['alamat', 'telepon', 'foto_profil', 'jabatan'],
            'BENDAHARA' => ['alamat', 'telepon', 'foto_profil', 'jabatan'],
        ];

        $role = $request->input('role');

        foreach ($roleFields[$role] as $field) {
            $rules[$field] = in_array($field, ['foto_profil', 'foto_jeep'])
                ? 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                : 'required|string';
        }

        $validated = $request->validate($rules);

        // Upload gambar jika ada
        if ($request->hasFile('foto_profil')) {
            $validated['foto_profil'] = $request->file('foto_profil')->store('profile_images', 'public');
        }

        if ($request->hasFile('foto_jeep')) {
            $validated['foto_jeep'] = $request->file('foto_jeep')->store('jeep_images', 'public');
        }

        // Hash password
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    // Get all users (Hanya bisa diakses oleh FO yang login)
    public function index(Request $request)
    {
        $user = Auth::guard('fo')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token tidak valid atau tidak ada.'
            ], 401);
        }

        return response()->json(User::all());
    }

    // Get user
    public function me()
    {
        $user = Auth::guard('fo')->user(); // Ambil user dari token aktif

        // Cek role dan sesuaikan data yang ingin ditampilkan
        switch ($user->role) {
            case 'FO':
                $data = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ];
                break;

            case 'OWNER':
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

            case 'DRIVER':
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

            case 'PENGURUS':
                $data = [
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
                break;

            case 'BENDAHARA':
                $data = [
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
                case 'FO':
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'role' => $user->role,
                    ];

                case 'DRIVER':
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

                case 'OWNER':
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

                case 'PENGURUS':
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

                case 'BENDAHARA':
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
}
