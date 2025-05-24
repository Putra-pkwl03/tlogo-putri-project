<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salaries extends Model
{
    use HasFactory;

    protected $primaryKey = 'salaries_id';

    protected $fillable = [
        'user_id',
        'nama',
        'role',
        'no_lambung',
        'salarie',
        'total_salary',
        'payment_date',
        'ticketing_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ticketing()
    {
        return $this->belongsTo(Ticketing::class, 'ticketing_id', 'id'); // Gunakan salah satu relasi saja
    }
}
