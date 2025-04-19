<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function checkout(Request $request)
    {
        $data = $request->all();

        $data['order_id'] = 'TP/' . date('Ymd') . '/' . mt_rand(1000, 9999);

        if (!isset($data['payment_status'])) {
            $data['payment_status'] = 'unpaid';
        }

        $isDP = $request->payment_type === 'dp';
        $amountToPay = $isDP ? $data['gross_amount'] * 0.3 : $data['gross_amount'];

        $data['dp_amount'] = $amountToPay;
        $data['is_fully_paid'] = !$isDP;
        $data['due_date'] = Carbon::parse($data['tour_date'])->subDays(1);
        $data['is_refundable'] = true;
        $data['refund_status'] = 'not_requested';

        $order = Booking::create($data);
        $order->refresh();

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
                'gross_amount' => $amountToPay,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
        ];
        

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        
        try {
            Log::info('Sampai di sini, sebelum create PaymentTransaction');

            PaymentTransaction::create([
                'booking_id' => $order->booking_id,              
                'order_id' => $order->order_id,                    
                'amount' => $amountToPay,                          
                'payment_type' => $order->payment_type,            
                'payment_for' => $isDP ? 'dp' : 'full',           
                'transaction_time' => now(),                       
                'status' => 'pending',                           
                'payment_gateway' => 'midtrans',                  
                'snap_token' => $snapToken,                        
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
            ]);
        } catch (\Exception $e) {
            Log::error('PaymentTransaction failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment transaction.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
            'order' => $order,
        ]);
            

    }

    public function midtransNotif(Request $request)
    {
        $serverKey = config('midtrans.server_key');
    
        $payload = $request->all();
    
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];
        $transactionStatus = $payload['transaction_status'];
    
        $hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
    
        if ($hashed === $signatureKey) {
            $paymentTransaction = PaymentTransaction::where('order_id', $orderId)->first();
        
            if ($paymentTransaction) {
                $paymentTransaction->status = $transactionStatus;
                $paymentTransaction->save();
            
                $order = Booking::find($paymentTransaction->booking_id);
                if ($order) {
                    $order->booking_status = $transactionStatus;
                    $order->payment_status = 'paid';
                    $order->save();
                }
            }
        }
    
        return response()->json(['message' => 'Notification processed.']);
    }
}
