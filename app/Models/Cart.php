<?php

namespace App\Models;

use App\Models\baseModel;
use App\Models\Transaction;
use App\Models\Item;

class Cart extends baseModel {
    protected $fillable = ['Quantity', 'costPerItem', 'item_id', 'priceChanged'];
    protected $casts = ['priceChanged' => 'boolean'];
    protected $hidden = ['id', 'transaction_id', 'item_id', 'created_at', 'updated_at'];
    protected $with = ['Item:id,delta,itemable_id,itemable_type', 'Item.itemable:id,name,brand'];
    
    public function Item() {
        return $this->belongsTo(Item::class);
    }

    public function Transaction() {
    	return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function getProfitPerItemAttribute() {
        return $this->costPerItem - $this->Item->averageBuyPricePerItem;
    }

    public function getTotalProfitAttribute() {
        return $this->profitPerItem * $this->Quantity;
    }
}