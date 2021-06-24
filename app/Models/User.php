<?php

namespace App\Models;

use App\Scopes\GroupScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

use App\Models\Store;
use App\Models\Group;

class User extends Authenticatable {
    use HasApiTokens;
    use HasRolesAndAbilities;

    protected $fillable = [
        'name',
        'phone_number',
        'store_id',
        'password'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];
    protected $appends = ['permissions'];

    protected static function booted() {
        parent::booted();
        static::addGlobalScope(new GroupScope);
    }

    public function getIsOwnerAttribute() {
        return $this->hasOne(Group::class, 'owner_id')->exists();
    }

    function Store() {
        return $this->belongsTo(Store::Class);
    }

    function Stores() {
        return $this->hasManyThrough(Store::class, Group::class, 'owner_id');
    }

    public function getPermissionsAttribute() {
        return $this->getAbilities()->pluck('name');
    }
}