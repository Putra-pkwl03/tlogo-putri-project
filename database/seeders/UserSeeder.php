<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'tp kaliurang',
            'username' => 'tlogoputri',
            'email_verified_at' => now(),
            'role' => 'FO',
            'email' => 'tlogoputri@gmail.com',
            'password' => Hash::make('tlogoputri000')
        ]);
        
    }
}