<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class baseModel extends Model {
    use FilterQueryString;
    
    protected $filters = ['like'];
    protected $hidden = ['store_id'];

    public static function boot() {
        parent::boot();

        static::addGlobalScope('store_id', function (Builder $builder) {
            $builder->where('store_id', auth()->user()->Store->id);
        });

        static::creating(function($model){
            $user = Auth::user();

            $model->created_by = $user->id;
            $model->updated_by = $user->id;

            $model->store_id = $user->store_id;
        });
        
        static::updating(function($model){
            $user = Auth::user();
            $model->updated_by = $user->id;
        });     
    }

    public function getCreatedByAttribute($value){
        return $this->theAttributes($value);
    }

    public function getUpdatedByAttribute($value){
        return $this->theAttributes($value);
    }

    public function theAttributes($value){
        $theUser = User::find($value);
        if ($theUser) return $theUser->name;
        
        return '';
    }
}