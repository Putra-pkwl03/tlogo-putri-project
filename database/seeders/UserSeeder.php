<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'TP Kaliurang',
            'username' => 'tlogoputri',
            'role' => 'Front Office',
            'email' => 'tlogoputri@gmail.com',
            'password' => Hash::make('tlogoputri000'),
            'remember_token' => Str::random(10),
        ]);

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