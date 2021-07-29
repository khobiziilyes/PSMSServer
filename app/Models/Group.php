<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Store;
use App\Models\User;

class Group extends Model {
    function Owner() {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    function Stores() {
        return $this->hasMany(Store::class);
    }
}