<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = [];
        $baseDate = Carbon::create(2025, 5, 1);

        for ($i = 1; $i <= 10; $i++) {
            $tourDate = $baseDate->copy()->addDays($i);
            $createdAt = $tourDate->copy()->subDays(5)->subHours(rand(1, 6));
            $updatedAt = $createdAt->copy()->addMinutes(rand(1, 10));

            $qty = rand(1, 3);
            $grossAmount = collect([300000, 450000, 400000])->random() ;
            $totalPrice = $qty * $grossAmount;

            $paymentType = collect(['dp', 'full'])->random();
            $isFullyPaid = $paymentType === 'full' ? 1 : collect([0, 1])->random();
            $dpAmount = $paymentType === 'dp' ? round($totalPrice * 0.3, 2) : $totalPrice;
            $orderId = 'TP-' . $createdAt->format('Ymd') . '-' . (5050 + $i);
            $paymentStatus = $paymentType === 'full' || $isFullyPaid ? 'paid' : 'unpaid';
            $bookingStatus = $paymentStatus === 'paid' ? 'settlement' : collect(['pending', 'expire', 'cancel'])->random();

            $bookings[] = [
                'booking_id' => $i,
                'customer_name' => 'test' . $i,
                'customer_email' => 'cust' . $i . '@example.com',
                'customer_phone' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'package_id' => rand(1, 3),
                'tour_date' => $tourDate->format('Y-m-d'),
                'start_time' => rand(7, 10) . ':00:00',
                'qty' => $qty,
                'gross_amount' => $grossAmount,
                'booking_status' => $bookingStatus,
                'order_id' => $orderId,
                'payment_status' => $paymentStatus,
                'payment_type' => $paymentType,
                'dp_amount' => $dpAmount,
                'is_fully_paid' => $isFullyPaid,
               'due_date' => $paymentType === 'dp' 
                ? $tourDate->copy()->subDay()->format('Y-m-d 00:00:00') 
                : $tourDate->copy()->subDay()->format('Y-m-d 00:00:00'),
                'referral' => null,
                'voucher' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }

        Booking::insert($bookings);
    }
}
