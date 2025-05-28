<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $potonganValues = [10, 15, 20];

        foreach ($potonganValues as $potongan) {
            Voucher::updateOrCreate(
                ['code' => 'putrijeep' . $potongan], 
                ['discount' => $potongan]
            );
        }
    }
}
