<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = ['username', 'email', 'password'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed', // Hash otomatis jika pakai Laravel 10+
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey(); // Mengembalikan ID sebagai subject token
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
