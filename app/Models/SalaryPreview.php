<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticketing_id',
        'nama',
        'role',
        'payment_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketing()
    {
        return $this->belongsTo(Ticketing::class);
    }
}