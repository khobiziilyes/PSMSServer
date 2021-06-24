<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Store;
use App\Models\User;

class Group extends Model {
    function Owner() {
        return $this->hasOne(User::class, 'owner_id');
    }

    function Stores() {
        return $this->hasMany(Store::class);
    }

    function Workers($removeOwner = true) {
    	$Stores_ids = $this->Stores()->pluck('stores.id')->toArray();
    	$query = User::whereIn('store_id', $Stores_ids);
    	
    	if ($removeOwner) $query->where('id', '!=', auth()->user()->id);

    	return $query;
    }
}