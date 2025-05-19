<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Rild Void',
            'username' => 'Rild',
            'email' => 'rildvoid@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('rildfnc'),
            'role' => 'Driver',
            'alamat' => 'Gamping',
            'telepon' => '0851234567',
            'foto_profil' => null,
            'tanggal_bergabung' => now()->toDateString(),
            'status' => 'Aktif',
            'konfirmasi' => 'Bisa',
            'jumlah_jeep' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}