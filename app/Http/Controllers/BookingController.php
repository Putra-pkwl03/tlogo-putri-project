<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function checkout(Request $request)
    {
        $data = $request->all();

        $lastOrder = Booking::orderBy('booking_id', 'desc')->first();
        $orderNumber = $lastOrder ? $lastOrder->booking_id + 1 : 1;
        $data['order_id'] = 'TP/' . date('Ymd') . '/' . str_pad($orderNumber, 4, '0', STR_PAD_LEFT);

        if (!isset($data['payment_status'])) {
            $data['payment_status'] = 'unpaid';
        }

        $order = Booking::create($data);

        /*Install Midtrans PHP Library (https://github.com/Midtrans/midtrans-php)
        composer require midtrans/midtrans-php*/
        
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => $order->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
        ];
        

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
            'order' => $order,
        ]);
    }
}
