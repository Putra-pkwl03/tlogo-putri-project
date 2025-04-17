<?php

namespace App\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackage extends Model
{
    protected $table = 'tour_packages';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'package_id');
    }

}
