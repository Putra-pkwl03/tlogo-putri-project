<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JeepSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jeeps')->insert([
            [
                'users_id' => 2, // User pemilik jeep (Sky)
                'owner_id' => 2,
                'driver_id' => 1, // Rild
                'no_lambung' => 'JEP-001',
                'plat_jeep' => 'AB 3507 JF',
                'foto_jeep' => null,
                'merek' => 'Jeep Wrangler',
                'tipe' => 'SUV',
                'tahun_kendaraan' => 2018,
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'users_id' => 2, // Masih milik Sky
                'owner_id' => 2,
                'driver_id' => 3, // Dava
                'no_lambung' => 'JEP-002',
                'plat_jeep' => 'AB 3508 JF',
                'foto_jeep' => null,
                'merek' => 'Jeep Cherokee',
                'tipe' => 'SUV',
                'tahun_kendaraan' => 2020,
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
