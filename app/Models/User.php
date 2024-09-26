<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens as Passport;

class User extends Authenticatable
{
    use Notifiable;
    use /*HasApiTokens*/ Passport, HasFactory, Notifiable;

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'password',
        'image_url',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeByRole($query,$role){
        if($role){
            return $query->where('role',$role);
        }
        return $query;
    }
}
