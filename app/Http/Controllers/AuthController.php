<?php

namespace App\Http\Controllers;

use App\Models\FO;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

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
    public function logout()
    {
        Auth::guard('fo')->logout();
        return response()->json([
            'success' => true,
            'message' => 'Anda telah berhasil logout.'
        ]);
    }

    // Refresh token
    public function refresh()
    {
        $token = Auth::guard('fo')->refresh();
        return $this->respondWithToken($token);
    }

    // Helper untuk return token JWT
    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('fo')->factory()->getTTL() * 60
        ]);
    }
}