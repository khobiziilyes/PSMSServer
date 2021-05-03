<?php

namespace App\Models;

use App\Models\Accessory;
use App\Models\Phone;
use App\Models\Transaction;

class Item extends baseModel {
    protected $fillable = ['delta', 'currentQuantity', 'defaultPrice', 'notes'];
    protected $with = ['itemable:id,name,brand'];
    protected $appends = ['isPhone'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden[] = 'itemable_type';
        $this->hidden[] = 'itemable_id';
    }

    public function Transactions() {
        return $this->hasMany(Transaction::class);
    }
    
    public function Itemable() {
        return $this->morphTo();
    }

    public function getIsPhoneAttribute() {
        return $this->Itemable()->firstOrFail()->isPhone;
    }
    
    public function changeQuantityBy($by) {
        if (($by < 0) && ($this->currentQuantity < abs($by))) {
            return false;
        } else {
            $this->currentQuantity += $by;
            $this->save();

            return $this;
        }
    }
}