<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\baseModel;
use App\Models\Item;

use App\Models\Vendor;
use App\Models\Customer;

class Transaction extends baseModel {
    use SoftDeletes;
    
    protected $fillable = ['cart', 'person_id', 'notes'];
    protected $table = 'transactions';
    protected $casts = ['isBuy' => 'boolean', 'priceChanged' => 'boolean', 'cart' => 'array'];
    protected $appendWith = ['Person:id,name'];
    protected $appendHidden = ['updated_at', 'updated_by', 'person_id', 'isBuy'];

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
    
    public function getCartAttribute($value) {
        $Obj = json_decode($value);

        $ids = array_map(function($Cart_Item) {
            return $Cart_Item->item_id;
        }, $Obj);

        $Items = Item::select('id', 'delta', 'itemable_id', 'itemable_type')->find($ids)->makeHidden([
            'mediumSellPrice',
            'mediumBuyPrice',
            'totalProfitPrice',
            'mediumProfitPrice',
            'expectedTotalProfitPrice'
        ])->keyBy('id');
        
        return array_map(function($Cart_Item) use ($Items) {
            return [
                'Item' => $Items[$Cart_Item->item_id],
                'Quantity' => $Cart_Item->Quantity,
                'costPerItem' => $Cart_Item->costPerItem,
                'priceChanged' => $Cart_Item->priceChanged,
            ];
        }, $Obj);
    }

    public function Person() {
        return $this->belongsTo(static::$isBuy ? Vendor::class : Customer::class);
    }
}

class Buy extends Transaction { static $isBuy = true; }
class Sell extends Transaction { static $isBuy = false; }