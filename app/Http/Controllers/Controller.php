<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Controller extends \Illuminate\Routing\Controller
{
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

    public function index()
    {
        return response()->json(User::all());
    }
}
