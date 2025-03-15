<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FO;
use Illuminate\Support\Facades\Hash; 

class FOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         FO::create([
            'name' => 'tp kaliurang',
            'email' => 'tlogoputri@gmail.com',
            'password' => Hash::make('tlogoputri000')
        ]);
    }
}