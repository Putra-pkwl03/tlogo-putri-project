<?php 

namespace App\Services;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Models\Voucher;

class BookingService
{
    public function createBooking($request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'package_id' => 'required|exists:tour_packages,id',
            'tour_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'qty' => 'required|integer|min:1',
            'gross_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:dp,full',    
        ]);

        $data = $request->only([
            'customer_name', 'customer_email', 'customer_phone', 'package_id',
            'tour_date', 'start_time', 'qty', 'gross_amount', 'payment_type', 'voucher', 'referral'
        ]);


        $data['order_id'] = 'TP-' . date('Ymd') . '-' . mt_rand(1000, 9999);

        if (!isset($data['payment_status'])) {
            $data['payment_status'] = 'unpaid';
        }

        $isDP = $request->payment_type === 'dp';
        $totalAmount = $data['gross_amount'] *  $data['qty'];

        $discountAmount = 0;
        if (!empty($data['voucher'])) {
            $voucher = Voucher::where('code', $data['voucher'])->first();

            if ($voucher) {
                $discountAmount = $totalAmount * ($voucher->discount / 100);
                $totalAmount -= $discountAmount;
            } else {
                Log::warning('Invalid voucher code: ' . $data['voucher']);
                return response()->json(['message' => 'Invalid voucher code'], 422);
            }
        }
        
        $amountToPay = $isDP ? $totalAmount * 0.5 : $totalAmount;

        $data['dp_amount'] = $amountToPay;
        $data['is_fully_paid'] = !$isDP;
        $data['due_date'] = Carbon::parse($data['tour_date'])->subDays(1);
        
        $order = Booking::create($data);
        Log::info(('Booking Created: ' . json_encode($order)));
        $order->refresh();

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
            'notification_url' => config('midtrans.notification_url'),
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
                ? config('remainpayment.remaining_payment_url') . '?order_id=' . urlencode($order->order_id)
                : null,
        ];

        // $pdf = PDF::loadView('pdf.payment_receipt', compact('order', 'transaction'));

        try {
            // Mail::to($order->customer_email)->send(new \App\Mail\PaymentReceiptMail($emailData));
            Mail::to($order->customer_email)
            ->later(now()->addSeconds(10), new \App\Mail\PaymentReceiptMail($emailData));

            Log::info("Email sent to " . $order->customer_email);
        } catch (\Exception $e) {
            Log::error("Email failed send to " . $order->customer_email . " with error: " . $e->getMessage());
        }
    }
}

    

?>