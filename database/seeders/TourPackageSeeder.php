<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TourPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        DB::table('tour_packages')->insert([
            [
                'package_name' => 'Paket 1',
                'slug' => 'paket-1',
                'description' => 'Description for tour package 1',
                'price' => 400000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 2',
                'slug' => 'paket-2',
                'description' => 'Description for tour package 2',
                'price' => 450000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 3',
                'slug' => 'paket-3',
                'description' => 'Description for tour package 3',
                'price' => 450000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 4',
                'slug' => 'paket-4',
                'description' => 'Description for tour package 4',
                'price' => 500000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 5',
                'slug' => 'paket-5',
                'description' => 'Description for tour package 5',
                'price' => 550000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket Sunrice',
                'slug' => 'paket-sunrice',
                'description' => 'Description for tour package 5',
                'price' => 550000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
