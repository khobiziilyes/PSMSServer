<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\Item;

class Cart extends Model {
    protected $fillable = ['Quantity', 'costPerItem', 'item_id', 'priceChanged'];
    protected $casts = ['priceChanged' => 'boolean'];
    protected $hidden = ['id', 'transaction_id', 'item_id'];
    protected $with = ['item:id,delta,itemable_id,itemable_type'];
    public $timestamps = false;

    public function Item() {
        return $this->belongsTo(Item::class);
    }

    public function Transaction() {
    	return $this->belongsTo(Transaction::class, 'id', 'transaction_id');
    }
}
