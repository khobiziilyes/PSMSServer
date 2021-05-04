<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class baseModel extends Model {
    use FilterQueryString;
    
    protected $filters = ['like'];
    protected $hidden = ['store_id', 'created_by_id', 'updated_by_id'];
    
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        
        $this->filters = array_merge($this->filters, $this->appendFilters ?? []);
        $this->with = array_merge($this->with ?? [], $this->appendWith ?? []);
        $this->appends = array_merge($this->appends ?? [], $this->appendAppends ?? []);
        $this->hidden = array_merge($this->hidden ?? [], $this->appendHidden ?? []);
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

            $model->store_id = $user->store_id;
        });
        
        static::updating(function($model) {
            $user = Auth::user();
            $model->updated_by_id = $user->id;
        });     
    }

    public function created_by() {
        return $this->hasOne(\App\Models\User::class, 'id', 'created_by_id');
    }

    public function updated_by() {
        return $this->hasOne(\App\Models\User::class, 'id', 'updated_by_id');   
    }
}