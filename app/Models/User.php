<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role'];
    protected $hidden = ['password','remember_token'];

    public function orders() 
    { 
        return $this->hasMany(Order::class); 
    }
    public function carts()  
    { 
        return $this->hasMany(Cart::class); 
    }

    public function isAdmin(): bool { 
        return $this->role === 'admin'; 
    }
}
