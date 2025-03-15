<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; 

class FO extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'fo'; 

    protected $fillable = ['name', 'email', 'password']; 

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

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