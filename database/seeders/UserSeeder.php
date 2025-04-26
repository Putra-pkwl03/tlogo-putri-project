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
            'name' => 'tp kaliurang',
            'username' => 'tlogoputri',
            'role' => 'Front Office',
            'email' => 'tlogoputri@gmail.com',
            'password' => Hash::make('tlogoputri000'),
            'remember_token' => Str::random(10),
        ]);
    }
}