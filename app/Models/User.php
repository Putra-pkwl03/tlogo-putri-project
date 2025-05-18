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

    // protected $fillable = ['name', 'email', 'password', 'role'];
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'alamat',
        'telepon',
        'foto_profil',
        'status',
        'tanggal_bergabung',
        'jumlah_jeep',
        'konfirmasi'
    ];

    // Implementasi metode dari JWTSubject
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function tickets()
    {
        return $this->hasMany(Ticketing::class, 'driver_id');
    }
}
