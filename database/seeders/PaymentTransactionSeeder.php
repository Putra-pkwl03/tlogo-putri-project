<?php

namespace Database\Seeders;

use App\Models\PaymentTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use Carbon\Carbon;

class PaymentTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [];

        Booking::all()->each(function (Booking $booking) use (&$transactions) {
            $createdAt = Carbon::parse($booking->created_at);
            $updatedAt = Carbon::parse($booking->updated_at);
            $totalPrice = $booking->qty * $booking->gross_amount;
            $dpAmount = $booking->payment_type === 'dp'
                ? round($totalPrice * 0.3, 2)
                : $totalPrice;
            
            // DP payment
            if ($booking->payment_type === 'dp') {
                $transactions[] = [
                    'booking_id'   => $booking->booking_id,
                    'order_id'     => $booking->order_id,
                    'amount'       => $dpAmount,
                    'payment_for'  => $booking->payment_type,
                    'status'       => $booking->payment_status === 'paid' ? 'settlement' : 'pending',
                    'channel'      => 'qris',
                    'expired_time' => $createdAt->copy()->addMinutes(30),
                    'snap_token'   => $booking->order_id . '-token-dp',
                    'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $booking->order_id . '-dp',
                    'created_at'   => $createdAt,
                    'updated_at'   => $updatedAt,
                ];

                // Pelunasan jika sudah fully paid
                if ($booking->is_fully_paid) {
                    $transactions[] = [
                        'booking_id'   => $booking->booking_id,
                        'order_id'     => $booking->order_id . '-2',
                        'amount'       => round($totalPrice - $dpAmount, 2),
                        'payment_for'  => 'remaining',
                        'status'       => 'settlement',
                        'channel'      => 'qris',
                        'expired_time' => $createdAt->copy()->addDays(2),
                        'snap_token'   => $booking->order_id . '-token-pelunasan',
                        'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $booking->order_id . '-pelunasan',
                        'created_at'   => $createdAt->copy()->addDay(),
                        'updated_at'   => $createdAt->copy()->addDay()->addMinutes(5),
                    ];
                }
            }
            // Full payment
            else {
                $transactions[] = [
                    'booking_id'   => $booking->booking_id,
                    'order_id'     => $booking->order_id,
                    'amount'       => $totalPrice,
                    'payment_for'  => 'full',
                    'status'       => 'settlement',
                    'channel'      => 'qris',
                    'expired_time' => $createdAt->copy()->addMinutes(30),
                    'snap_token'   => $booking->order_id . '-token-full',
                    'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $booking->order_id . '-full',
                    'created_at'   => $createdAt,
                    'updated_at'   => $updatedAt,
                ];
            }
        });

        PaymentTransaction::insert($transactions);
    }
}
