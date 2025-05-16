<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticketing extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_booking',
        'nama_pemesan',
        'no_handphone',
        'email',
        'driver_id',
        'jeep_id',
        'booking_id',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id')->where('role', 'Driver');
    }

    public function jeep()
    {
        return $this->belongsTo(Jeep::class, 'jeep_id', 'jeep_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
