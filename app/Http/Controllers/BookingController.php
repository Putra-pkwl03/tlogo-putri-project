<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

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

                    if (in_array($transactionStatus, ['capture', 'settlement'])) {
                        $this->sendPaymentReceipt($order, $paymentTransaction);
                    }
                }
            }
        }
    
        return response()->json(['message' => 'Notification processed.']);
    }

    protected function sendPaymentReceipt($order, $transaction)
    {
        $emailData = [
            'name' => $order->customer_name,
            'order_id' => $transaction->order_id,
            'amount' => $transaction->amount,
            'payment_type' => $order->payment_type,
            'tour_date' => $order->tour_date,
            'is_dp' => $order->payment_type === 'dp',
            'remaining_url' => $order->payment_type === 'dp'
                ? url('/pelunasan/' . $order->id)
                : null,
        ];

        $pdf = PDF::loadView('pdf.payment_receipt', compact('order', 'transaction'));

        try {
            Mail::to($order->customer_email)->send(new \App\Mail\PaymentReceiptMail($emailData, $pdf));
            Log::info("Email sent to " . $order->customer_email);
        } catch (\Exception $e) {
            Log::error("Email failed to send to " . $order->customer_email . " with error: " . $e->getMessage());
        }
        
    }

}
