<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'last_assigned_at',
        'alamat',
        'telepon',
        'foto_profil',
        'status',
        'tanggal_bergabung',
        'jumlah_jeep',
        'konfirmasi'
    ];

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relasi untuk user dengan role Driver → ke ticketing
    public function tickets()
    {
        return $this->hasMany(Ticketing::class, 'driver_id');
    }

    public function rotations()
    {
        return $this->hasMany(DriverRotation::class, 'driver_id');
    }

    // ✅ Relasi sebagai pemilik jeep (Owner)
    public function jeepsOwned()
    {
        return $this->hasMany(Jeep::class, 'owner_id');
    }

    // ✅ Relasi sebagai pengemudi jeep (Driver)
    public function jeepsDriven()
    {
        return $this->hasMany(Jeep::class, 'driver_id');
    }
}
