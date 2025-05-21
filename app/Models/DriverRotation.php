<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class DriverRotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'driver_id',
        'assigned',
        'skip_reason',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}