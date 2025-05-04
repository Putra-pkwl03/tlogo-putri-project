<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JeepSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jeeps')->insert([
            [
                'users_id' => 2,
                'plat_jeep' => 'AB 3507 JF',
                'foto_jeep' => null,
                'merek' => 'Jeep Wrangler',
                'tipe' => 'SUV',
                'tahun_kendaraan' => 2018,
                'status' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
