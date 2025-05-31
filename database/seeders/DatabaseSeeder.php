<?php

use App\Models\User;
use Illuminate\Support\Str;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\JeepSeeder;
use Database\Seeders\TourPackageSeeder;
use Database\Seeders\BookingSeeder;
use Database\Seeders\PaymentTransactionSeeder;
use Database\Seeders\VoucherSeeder;
use Database\Seeders\ArtikelSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            JeepSeeder::class,
            TourPackageSeeder::class,
            BookingSeeder::class,
            PaymentTransactionSeeder::class,
            VoucherSeeder::class,
            ArtikelSeeder::class
        ]);
    }
}
