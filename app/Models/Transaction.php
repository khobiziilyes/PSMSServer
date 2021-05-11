<?php

namespace App\Models;

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
    protected $_hidden = ['updated_at', 'updated_by', 'person_id'];
    
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->makeHiddenIf(isset(static::$isBuy), 'isBuy');
    }
    
    public static function boot() {
        parent::boot();

        if (isset(static::$isBuy)) {
            static::addGlobalScope('isBuy', function (Builder $builder) {
                $builder->where('isBuy', static::$isBuy);
            });
        }
        
        static::creating(function($model) {
            $model->isBuy = static::$isBuy;
        });

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
}

class Buy extends Transaction { static $isBuy = true; }
class Sell extends Transaction { static $isBuy = false; }