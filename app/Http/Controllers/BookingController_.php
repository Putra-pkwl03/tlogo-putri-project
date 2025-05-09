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
    // public function checkout(Request $request)
    // {
    //     $data = $request->all();

    //     $data['order_id'] = 'TP-' . date('Ymd') . '-' . mt_rand(1000, 9999);

    //     if (!isset($data['payment_status'])) {
    //         $data['payment_status'] = 'unpaid';
    //     }

    //     $isDP = $request->payment_type === 'dp';
    //     $totalAmount = $data['gross_amount'] *  $data['qty'];
    //     $amountToPay = $isDP ? $totalAmount * 0.3 : $totalAmount;

    //     $data['dp_amount'] = $amountToPay;
    //     $data['is_fully_paid'] = !$isDP;
    //     $data['due_date'] = Carbon::parse($data['tour_date'])->subDays(1);
        
    //     $order = Booking::create($data);
    //     Log::info(($order));
    //     $order->refresh();

    //     /*Install Midtrans PHP Library (https://github.com/Midtrans/midtrans-php)
    //     composer require midtrans/midtrans-php*/
        
    //     // Set your Merchant Server Key
    //     \Midtrans\Config::$serverKey = config('midtrans.server_key');
    //     // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
    //     \Midtrans\Config::$isProduction = false;
    //     // Set sanitization on (default)
    //     \Midtrans\Config::$isSanitized = true;
    //     // Set 3DS transaction for credit card to true
    //     \Midtrans\Config::$is3ds = true;

    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $order->order_id,
    //             'gross_amount' => $amountToPay,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $order->customer_name,
    //             'email' => $order->customer_email,
    //             'phone' => $order->customer_phone,
    //         ],
    //     ];
        

    //     $snapToken = \Midtrans\Snap::getSnapToken($params);
        
    //     try {
    //         Log::info('Creating PaymentTransaction');

    //         PaymentTransaction::create([
    //             'booking_id' => $order->booking_id,              
    //             'order_id' => $order->order_id,                    
    //             'amount' => $amountToPay,                                 
    //             'payment_for' => $isDP ? 'dp' : 'full',              
    //             'status' => 'pending',                                             
    //             'snap_token' => $snapToken,                        
    //             'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('PaymentTransaction failed: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create payment transaction.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'snap_token' => $snapToken,
    //         'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
    //         'order' => $order,
    //     ]);
            

    // }

    // public function midtransNotif(Request $request)
    // {
    //     $serverKey = config('midtrans.server_key');

    //     $payload = $request->all();
    //     Log::info('Midtrans Notification Payload:', $request->all());

    //     $orderId = $payload['order_id'];
    //     $statusCode = $payload['status_code'];
    //     $grossAmount = $payload['gross_amount'];
    //     $signatureKey = $payload['signature_key'];

    //     $transactionStatus = $payload['transaction_status'];
    //     $channel = $payload['payment_type'];
    //     $expiredTime = $payload['expiry_time'];
    
    //     $hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
    
    //     if ($hashed === $signatureKey) {
    //         $paymentTransaction = PaymentTransaction::where('order_id', $orderId)->first();
        
    //         if ($paymentTransaction) {
    //             $paymentTransaction->status = $transactionStatus;
    //             $paymentTransaction->channel = $channel;
    //             $paymentTransaction->expired_time = $expiredTime;
    //             $paymentTransaction->save();
        
    //             $order = Booking::with('package')->find($paymentTransaction->booking_id);
    //             if ($order) {
    //                 $order->booking_status = $transactionStatus;
        
    //                 // Atur payment_status berdasarkan payment_type dan payment_for
    //                 if ($order->payment_type === 'dp') {
    //                     if ($paymentTransaction->payment_for === 'dp') {
    //                         // Baru DP saja
    //                         $order->payment_status = 'unpaid';
    //                     } elseif ($paymentTransaction->payment_for === 'remaining') {
    //                         // Sudah bayar pelunasan
    //                         $order->payment_status = 'paid';
    //                         $order->is_fully_paid = true;
    //                     }
    //                 } else {
    //                     // Full payment
    //                     $order->payment_status = 'paid';
    //                     $order->is_fully_paid = true;
    //                 }
        
    //                 $order->save();
        
    //                 if (in_array($transactionStatus, ['capture', 'settlement'])) {
    //                     $this->sendPaymentReceipt($order, $paymentTransaction);
    //                 }
    //             }
    //         }
    //     }
        
    
    //     return response()->json(['message' => 'Notification processed.']);
    // }

    // protected function sendPaymentReceipt($order, $transaction)
    // {
    //     $emailData = [
    //         'name' => $order->customer_name,
    //         'order_id' => $transaction->order_id,
    //         'payment_type' => $order->payment_type,
    //         'payment_method' => $transaction->channel,
    //         'payment_status' => $order->payment_status,
    //         'expired_time' => $transaction->expired_time,
    //         'tour_date' => $order->tour_date,
    //         'package_type' => $order->package->package_name,
    //         'package_price' => $order->package->price,
    //         'start_time' => $order->start_time,
    //         'qty'=> $order->qty,
    //         'amount' => $transaction->amount,
    //         'total_price' => $order->qty * $order->gross_amount,
    //         'remain_amount' => $order->gross_amount - $order->dp_amount,
    //         'remaining_url' => $order->payment_type === 'dp'
    //             ? url('/api/orders/' . urlencode($order->order_id) . '/remaining-payment')
    //             : null,
    //     ];

    //     $pdf = PDF::loadView('pdf.payment_receipt', compact('order', 'transaction'));

    //     try {
    //         Mail::to($order->customer_email)->send(new \App\Mail\PaymentReceiptMail($emailData, $pdf));
    //         Log::info("Email sent to " . $order->customer_email);
    //     } catch (\Exception $e) {
    //         Log::error("Email failed send to " . $order->customer_email . " with error: " . $e->getMessage());
    //     }
        
    // }

}
