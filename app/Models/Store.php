<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Store extends Model {    
    function Users() {
        return $this->hasMany(User::Class);
    }
}
