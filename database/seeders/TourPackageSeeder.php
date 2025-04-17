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
                'package_name' => 'Tour Package 1',
                'description' => 'Description for tour package 1',
                'price' => 100000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Tour Package 2',
                'description' => 'Description for tour package 2',
                'price' => 150000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Tour Package 3',
                'description' => 'Description for tour package 3',
                'price' => 200000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
