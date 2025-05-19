<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jeep extends Model
{
    use HasFactory;

    protected $primaryKey = 'jeep_id';

    protected $fillable = [
        'users_id',
        'owner_id',         // ganti dari users_id → owner_id
        'driver_id',        // tambahkan jika jeep punya driver
        'no_lambung',
        'plat_jeep',
        'foto_jeep',
        'merek',
        'tipe',
        'tahun_kendaraan',
        'status',
    ];

    // Relasi ke Ticketing
    public function tickets()
    {
        return $this->hasMany(Ticketing::class, 'jeep_id', 'jeep_id');
    }

    // Relasi ke User dengan role Owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->where('role', 'Owner');
    }

    // Relasi ke User dengan role Driver
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id')->where('role', 'Driver');
    }
}
