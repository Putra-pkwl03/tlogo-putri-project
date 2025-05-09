<?php 

namespace App\Services;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    public function createBooking($request)
    {
        $data = $request->all();

        $data['order_id'] = 'TP-' . date('Ymd') . '-' . mt_rand(1000, 9999);

        if (!isset($data['payment_status'])) {
            $data['payment_status'] = 'unpaid';
        }

        $isDP = $request->payment_type === 'dp';
        $totalAmount = $data['gross_amount'] *  $data['qty'];
        $amountToPay = $isDP ? $totalAmount * 0.3 : $totalAmount;

        $data['dp_amount'] = $amountToPay;
        $data['is_fully_paid'] = !$isDP;
        $data['due_date'] = Carbon::parse($data['tour_date'])->subDays(1);
        
        $order = Booking::create($data);
        Log::info(('Booking Created: ' . json_encode($order)));
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
            Log::info('Creating PaymentTransaction');

            PaymentTransaction::create([
                'booking_id' => $order->booking_id,              
                'order_id' => $order->order_id,                    
                'amount' => $amountToPay,                                 
                'payment_for' => $isDP ? 'dp' : 'full',              
                'status' => 'pending',                                             
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

        return [
            'order' => $order,
            'snap_token' => $snapToken,
        ];
    }

    public function sendPaymentReceipt($order, $transaction)
    {
        $emailData = [
            'name' => $order->customer_name,
            'order_id' => $transaction->order_id,
            'payment_type' => $order->payment_type,
            'payment_method' => $transaction->channel,
            'payment_status' => $order->payment_status,
            'expired_time' => $transaction->expired_time,
            'tour_date' => $order->tour_date,
            'package_type' => $order->package->package_name,
            'package_price' => $order->package->price,
            'start_time' => $order->start_time,
            'qty'=> $order->qty,
            'amount' => $transaction->amount,
            'total_price' => $order->qty * $order->gross_amount,
            'remain_amount' => ($order->gross_amount * $order->qty) - $order->dp_amount,
            'remaining_url' => $order->payment_type === 'dp'
                ? url('/api/orders/' . urlencode($order->order_id) . '/remaining-payment')
                : null,
        ];

        $pdf = PDF::loadView('pdf.payment_receipt', compact('order', 'transaction'));

        try {
            Mail::to($order->customer_email)->send(new \App\Mail\PaymentReceiptMail($emailData, $pdf));
            Log::info("Email sent to " . $order->customer_email);
        } catch (\Exception $e) {
            Log::error("Email failed send to " . $order->customer_email . " with error: " . $e->getMessage());
        }
    }
}

    

?>