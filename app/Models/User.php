<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Models\Store;

class User extends Authenticatable {
    use HasApiTokens;

    protected $fillable = [
        'name',
        'phone_number',
        'store_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function Store() {
        return $this->belongsTo(Store::Class);
    }
}