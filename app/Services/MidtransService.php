<?php 
namespace App\Services;

use App\Models\Booking;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use App\Services\BookingService;


class MidtransService 
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    
    public function handleNotification($payload)
    {
        $serverKey = config('midtrans.server_key');

        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];

        $transactionStatus = $payload['transaction_status'];
        $channel = $payload['payment_type'];
        $expiredTime = $payload['expiry_time'];
    
        $hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
    
        if ($hashed === $signatureKey) {
            $paymentTransaction = PaymentTransaction::where('order_id', $orderId)->first();
        
            if ($paymentTransaction) {
                $paymentTransaction->status = $transactionStatus;
                $paymentTransaction->channel = $channel;
                $paymentTransaction->expired_time = $expiredTime;
                $paymentTransaction->save();
        
                $order = Booking::with('package')->find($paymentTransaction->booking_id);
                if ($order) {
                    $order->booking_status = $transactionStatus;
        
                    // Atur payment_status berdasarkan payment_type dan payment_for
                    if ($order->payment_type === 'dp') {
                        if ($paymentTransaction->payment_for === 'dp') {
                            // Baru DP saja
                            $order->payment_status = 'unpaid';
                        } elseif ($paymentTransaction->payment_for === 'remaining') {
                            // Sudah bayar pelunasan
                            $order->payment_status = 'paid';
                            $order->is_fully_paid = true;
                        }
                    } else {
                        // Full payment
                        $order->payment_status = 'paid';
                        $order->is_fully_paid = true;
                    }
        
                    $order->save();
        
                    if (in_array($transactionStatus, ['capture', 'settlement'])) {
                        $this->bookingService->sendPaymentReceipt($order, $paymentTransaction);
                    }
                }
            }
        }
    }

}

?>