<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run()
    {
        $potonganValues = [10, 15, 20];

        foreach ($potonganValues as $potongan) {
            Voucher::updateOrCreate(
                ['kode' => 'PROMO' . $potongan], 
                ['potongan' => $potongan]
            );
        }
    }
}