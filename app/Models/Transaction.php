<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;
use App\Models\Person;

class Transaction extends baseModel {
    protected $fillable = ['costPerItem', 'Quantity', 'person_id', 'item_id', 'notes'];
    protected $table = 'transactions';
    protected $casts = ['isBuy' => 'boolean', 'priceChanged' => 'boolean'];
    protected $with = ['Item:id,itemable_id,delta', 'Person:id,name'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden[] = 'updated_at';
        $this->hidden[] = 'updated_by';
        $this->hidden[] = 'item_id';
        $this->hidden[] = 'person_id';
        $this->hidden[] = 'isBuy';
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
    }
    
    public function Person() {
        return $this->belongsTo(Person::class);
    }

    public function Item() {
        return $this->belongsTo(Item::class);
    }
}

class Buy extends Transaction { static $isBuy = true; }
class Sell extends Transaction { static $isBuy = false; }