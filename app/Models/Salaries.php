<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salaries extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'role',
        'no_lambung',
        'salarie',
        'total_salary',
        'payment_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
