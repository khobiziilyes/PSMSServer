<?php

namespace App\Models;

class imei extends baseModel {
    protected $table = 'imei';
    protected $appends = ['isSold'];
    protected $fillable = ['item_id', 'imei', 'buy_payment_id'];
    protected $with = ['item:id,good_id,delta', 'item.good:id,name,brand'];
    
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->hidden[] = 'item_id';
    }

    public function getIsSoldAttribute() {
        return ($this->sell_payment_id !== null);
    }

    public function Item() {
    	return $this->belongsTo(Item::class);
    }
}
