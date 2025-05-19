<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
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
            'konfirmasi' => null,
            'jumlah_jeep' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Sky Sekuy',
            'username' => 'Sky',
            'email' => 'skysekuy@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('skysekuy'),
            'role' => 'Owner',
            'alamat' => 'Gamping',
            'telepon' => '0851234568',
            'foto_profil' => null,
            'tanggal_bergabung' => now()->toDateString(),
            'status' => 'Aktif',
            'konfirmasi' => null,
            'jumlah_jeep' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'name' => 'Dava Axel',
            'username' => 'Dava',
            'email' => 'dava.axel@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('dava12345'),
            'role' => 'Driver',
            'alamat' => 'Wirobrajan',
            'telepon' => '0859876543',
            'foto_profil' => null,
            'tanggal_bergabung' => now()->toDateString(),
            'status' => 'Aktif',
            'konfirmasi' => null,
            'jumlah_jeep' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
