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
                'destination' => 'offroad grogol, spot foto opak, museum mini, batu alien, track air',
                'price' => 400000,
                'image' => 'paket-1.JPG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 2',
                'slug' => 'paket-2',
                'destination' => 'offroad grogol, spot foto opak, museum mini, batu alien, the lost world park, track air',
                'price' => 450000,
                'image' => 'paket-2.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 3',
                'slug' => 'paket-3',
                'destination' => 'petilasan mbah marijan, spot foto opak, bungker kali adem, track tegong/tebing gendol, track air',
                'price' => 450000,
                'image' => 'paket-3.JPG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 4',
                'slug' => 'paket-4',
                'destination' => 'offroad grogol, petilasan mbah marijan, spot foto opak, bungker kali adem, batu alien/the lost world park, track air',
                'price' => 500000,
                'image' => 'paket-4.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket 5',
                'slug' => 'paket-5',
                'destination' => 'petilasan mbah marijan, spot foto opak, bunker kali adem, batu alien/the lost world park, museum mini, track air',
                'price' => 550000,
                'image' => 'paket-5.JPG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'Paket Sunrice',
                'slug' => 'paket-sunrice',
                'destination' => 'bungker kali adem, spot foto jeep, batu alien/tlwp, museum mini, track air',
                'price' => 550000,
                'image' => 'paket-sunrice.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
