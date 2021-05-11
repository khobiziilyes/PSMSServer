<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use Mehradsadeghi\FilterQueryString\FilterQueryString;

class baseModel extends Model {
    use FilterQueryString;
    
    protected $filters = ['like'];
    protected $hidden = ['store_id', 'created_by_id', 'updated_by_id', 'created_by_obj', 'updated_by_obj'];
    
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden ?? [], $this->_hidden ?? []);
    }

    public static function boot() {
        parent::boot();

        static::addGlobalScope('store_id', function (Builder $builder) {
            $builder->where('store_id', auth()->user()->Store->id);
        });

        static::creating(function($model) {
            $user = Auth::user();

            $model->created_by_id = $user->id;
            $model->updated_by_id = $user->id;

            $model->store_id = $user->Store->id;
        });
        
        static::updating(function ($model) { 
            $user_id = Auth::user()->id;
            $model->updated_by_id = $user_id;
        });

        static::deleting(function($model) {
            $user_id = Auth::user()->id;
            $model->updated_by_id = $user_id;

            $model->save();
        });
    }

    public function created_by_obj() {
        return $this->hasOne(\App\Models\User::class, 'id', 'created_by_id');
    }

    public function updated_by_obj() {
        return $this->hasOne(\App\Models\User::class, 'id', 'updated_by_id');   
    }

    public function getCreatedByAttribute () {
        return $this->created_by_obj->name;
    }

    public function getUpdatedByAttribute () {
        return $this->updated_by_obj->name;
    }
}