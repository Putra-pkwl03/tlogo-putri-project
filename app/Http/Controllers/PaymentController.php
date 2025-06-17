<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Services\BookingService;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function getRemainingPaymentInfo($order_id)
    {
        // $decodedOrderId = urldecode($order_id);
        $booking = Booking::with(['package:id,package_name'])->where('order_id', $order_id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        if ($booking->payment_type !== 'dp') {
            return response()->json(['message' => 'Bukan pembayaran DP'], 400);
        }

        $remainingAmount = ($booking->gross_amount * $booking->qty) - $booking->dp_amount;

        $response = [
            'booking_id' => $booking->booking_id,
            'order_id' => $booking->order_id,
            'customer_name' => $booking->customer_name,
            'customer_phone' => $booking->customer_phone,
            'gross_amount' => $booking->gross_amount,
            'total' => $booking->gross_amount * $booking->qty,
            'qty' => $booking->qty,
            'deposit' => $booking->dp_amount ,
            'remaining_amount' => $remainingAmount,
            'tour_date' => $booking->tour_date,
            'package' => $booking->package,
            'message' => 'Pembayaran belum lunas',
        ];

        if ($booking->payment_status === 'paid') {
            $response['message'] = 'Pembayaran DP Lunas';
        }
    
        return response()->json($response, 200);
    }


    public function startRemainingPayment($order_id)
    {
        // $decodedOrderId = urldecode($order_id);
        $booking = Booking::where('order_id', $order_id)->firstOrFail();

        
        if ($booking->payment_type !== 'dp' || $booking->payment_status === 'paid') {
            return response()->json(['message' => 'Tidak bisa diproses'], 400);
        }
        
        $remainingAmount = ($booking->gross_amount * $booking->qty) - $booking->dp_amount;
        
        $newTransaction = PaymentTransaction::create([
            'booking_id' => $booking->booking_id,
            'order_id' => $booking->order_id . '-2', 
            'amount' => $remainingAmount,
            'payment_for' => 'remaining',
            'status' => 'pending',            
        ]);

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
        
        // Snap token
        $params = [
            'transaction_details' => [
                'order_id' => $newTransaction->order_id,
                'gross_amount' => $newTransaction->amount,
            ],
            'customer_details' => [
                'first_name' => $booking->customer_name,
                'email' => $booking->customer_email,
            ],
        ];
        
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $newTransaction->update([
            'snap_token' => $snapToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v3/redirection/' . $snapToken,
            'order' => [
                'booking_id' => $newTransaction->booking_id,
                'order_id' => $newTransaction->order_id,
                'amount' => $newTransaction->amount,
                'payment_for' => $newTransaction->payment_for,
                'updated_at' => $newTransaction->updated_at,
                'created_at' => $newTransaction->created_at,
                'transaction_id' => $newTransaction->transaction_id,
            ],
        ]);
        
    }

    public function index()
    {
        // $transactions = PaymentTransaction::get();
        $transactions = PaymentTransaction::with('booking')
        ->select('transaction_id', 'booking_id','order_id', 'amount', 'channel','status', 'payment_for', 'created_at',)
        ->get();
        return response()->json($transactions, 200);
    }

    public function show($booking_id)
    {
        $transaction = PaymentTransaction::where('booking_id', $booking_id)
        ->select('transaction_id', 'booking_id','order_id', 'amount', 'channel', 'status', 'payment_for', 'created_at',)
        ->get();


        if ($transaction->isEmpty()) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction, 200);
    }


    public function syncStatus($orderId)
    {
        $serverKey = config('midtrans.server_key');
        $auth = base64_encode($serverKey . ':');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $auth,
        ])->get("https://api.sandbox.midtrans.com/v2/{$orderId}/status");

        if (!$response->ok()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status dari Midtrans.',
            ], 500);
        }

        $data = $response->json();
        $transactionStatus = $data['transaction_status'] ?? null;

        if (!$transactionStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }

        $payment = PaymentTransaction::where('order_id', $orderId)->first();

        if (!$payment) {
            $baseOrderId = implode('-', array_slice(explode('-', $orderId), 0, 3));
        
            $payment = PaymentTransaction::where('order_id', 'like', "{$baseOrderId}%")
                        ->where('payment_for', 'remaining')
                        ->orderByDesc('id')
                        ->first();
        }


        // Update data payment
        $payment->status = $transactionStatus;
        $payment->channel = $data['payment_type'] ?? null;
        $payment->expired_time = $data['expiry_time'] ?? null;
        $payment->save();

        // Update booking juga
        $order = Booking::with('package')->find($payment->booking_id);
        if ($order) {
            $order->booking_status = $transactionStatus;

            if ($order->payment_type === 'dp') {
                if ($payment->payment_for === 'dp') {
                    $order->payment_status = 'unpaid';
                } elseif ($payment->payment_for === 'remaining') {
                    $order->payment_status = 'paid';
                    $order->is_fully_paid = true;
                }
            } else {
                $order->payment_status = 'paid';
                $order->is_fully_paid = true;
            }

            $order->save();

            // Kirim bukti jika settlement
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $this->bookingService->sendPaymentReceipt($order, $payment);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil disinkronkan.',
            'data' => $data,
        ]);
    }


}
