<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';
    protected $primaryKey = 'transaction_id';
    
    protected $fillable = [
        'booking_id',
        'order_id',
        'amount',
        'payment_type',
        'payment_for',
        'transaction_time',
        'status',
        'payment_gateway',
        'snap_token',
        'redirect_url',
    ];
}
