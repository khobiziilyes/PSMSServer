<?php
namespace App\Models;

use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class baseModel extends Model {
    use Filterable;
    
    protected $hidden = ['store_id', 'created_by_id', 'updated_by_id', 'created_by_obj', 'updated_by_obj'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden ?? [], $this->_hidden ?? []);
    }

    public static function boot() {
        parent::boot();

        static::addGlobalScope('store_id', function (Builder $builder) {
            $builder->where('store_id', auth()->user()->Store->id);
            if (in_array(strtoupper(request()->method()), ['GET', 'HEAD', 'POST'])) $builder->orWhere('store_id', 0);
        });

        static::creating(function($model) {
            $user = Auth::user();

            $model->created_by_id = $user->id;
            $model->updated_by_id = $user->id;

            if (is_null($model->store_id)) $model->store_id = $user->Store->id;
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
        return $this->creatorUpdator('created_by_id');
    }

    public function updated_by_obj() {
        return $this->creatorUpdator('updated_by_id');
    }

    public function creatorUpdator($field) {
        return $this->hasOne(\App\Models\User::class, 'id', $field)->withDefault(['id' => 0, 'name' => 'PSMS']);
    }

    public function getCreatedByAttribute () {
        return $this->created_by_obj->name;
    }

    public function getUpdatedByAttribute () {
        return $this->updated_by_obj->name;
    }
}