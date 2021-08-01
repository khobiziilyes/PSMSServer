<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;

use App\Models\Cart;
use App\Models\Person;

class Transaction extends baseModel {
    use SoftDeletes;
    
    protected $fillable = ['person_id', 'notes'];
    protected $table = 'transactions';
    protected $casts = ['isBuy' => 'boolean'];
    protected $with = ['Person:id,name', 'Carts'];
    protected $hidden = ['updated_at', 'person_id'];
    protected $append = ['profit'];
    
    public static function boot() {
        parent::boot();
        
        static::deleting(function($model) {
            $user_id = Auth::user()->id;
            $model->updated_by_id = $user_id;

            $model->save();
        });
    }
    
    public function Carts() {
        return $this->hasMany(Cart::class, 'transaction_id', 'id');
    }

    public function Person() {
        return $this->belongsTo(Person::class);
    }

    public function getProfitAttribute() {
        return $this->Carts->sum('totalProfit');
    }
}