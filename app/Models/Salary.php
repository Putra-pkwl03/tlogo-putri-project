<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $table = 'salaries';

    protected $primaryKey = 'salaries_id';

    protected $fillable = [
        'user_id',
        'ticketing_id',
        'nama',
        'role',
        'no_lambung',
        'kas',
        'operasional',
        'salarie',
        'total_salary',
        'payment_date',
        'status',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke ticketing
    public function ticketing()
    {
        return $this->belongsTo(Ticketing::class, 'ticketing_id');
    }
}