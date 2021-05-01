<?php

namespace App\Models;

use App\Models\Accessory;
use App\Models\Phone;
use App\Models\Transaction;
use App\Models\Good;

class Item extends baseModel {
    protected $fillable = ['good_id', 'delta', 'currentQuantity', 'defaultPrice'];
    protected $appends = ['isPhone'];
    protected $with = ['good:id,name,brand'];
    
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden[] = 'good_id';
    }

    public function Transactions() {
        return $this->hasMany(Transaction::class);
    }
    
    public function Good() {
        return $this->belongsTo(Good::class);
    }

    public function getIsPhoneAttribute() {
        return Good::findOrFail($this->good_id)->isPhone;
    }
}