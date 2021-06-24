<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Group;
use App\Models\User;

class Store extends Model {
	protected $hidden = ['laravel_through_key'];

    function Users() {
        return $this->hasMany(User::Class);
    }

    function Group() {
    	return $this->belongsTo(Group::class);
    }
}