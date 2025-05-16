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
        'no_lambung',
        'plat_jeep',
        'foto_jeep',
        'merek',
        'tipe',
        'tahun_kendaraan',
        'status',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticketing::class, 'jeep_id', 'jeep_id');
    }
}
