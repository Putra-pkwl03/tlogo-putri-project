<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTicketing extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticketing_id',
        'code_booking',
        'nama_pemesan',
        'no_handphone',
        'email',
        'driver_id',
        'jeep_id',
        'booking_id',
        'activity',
        'changed_by',
    ];

    // Relasi ke tabel ticketings
    public function ticketing()
    {
        return $this->belongsTo(Ticketing::class);
    }

    // Relasi ke user yang melakukan perubahan (optional)
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Relasi ke driver
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Relasi ke jeep
    public function jeep()
    {
        return $this->belongsTo(Jeep::class, 'jeep_id', 'jeep_id');
    }

    // Relasi ke booking
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
