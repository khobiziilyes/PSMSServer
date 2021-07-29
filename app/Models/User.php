<?php

namespace App\Models;

use App\Scopes\GroupScope;

use Illuminate\Database\Eloquent\SoftDeletes;
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
    use SoftDeletes;
    
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

    public function Group() {
        return $this->hasOne(Group::class, 'owner_id');
    }
    
    public function getIsOwnerAttribute() {
        return (bool) $this->Group;
    }

    public function Store() {
        return $this->belongsTo(Store::Class);
    }

    public function Stores() {
        return $this->hasManyThrough(Store::class, Group::class, 'owner_id');
    }

    public function StoresForWorker() {
        return $this->Store->Group->Owner->Stores;
    }
    
    public function getPermissionsAttribute() {
        $abilities = $this->getAbilities()->pluck('name')->toArray();
        $ULTRA_PERMISSIONS = config('app.ULTRA_PERMISSIONS');
        
        return $ULTRA_PERMISSIONS->flatMap(function($permission) use($abilities) {
            return [$permission => in_array($permission, $abilities)];
        })->toArray();
    }
}