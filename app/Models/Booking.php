<?php

namespace App\Models;

use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'booking_id';

    protected $guarded = ['booking_id'];
    
    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'package_id');
    }
    
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'booking_id');
    }
}
