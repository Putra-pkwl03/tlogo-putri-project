<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; 



// KALAU ADA PERUBHAN DI DB SEBAIKNYA MIGRATE ROLLBACK BIAR TABEL DN CODE BERSI JANGAN LANGSUNG UPDATE
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users'; 

    // protected $fillable = ['name', 'email', 'password', 'role'];
    protected $fillable = [
        'name', 'username', 'email', 'password', 'role',
        'alamat', 'no_ktp', 'telepon', 'foto_profil',
        'status', 'tanggal_bergabung', 'jumlah_jeep',
        'plat_jeep', 'foto_jeep', 'jabatan'
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
}