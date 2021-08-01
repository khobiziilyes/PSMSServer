<?php

namespace App\Models;

use App\Models\Cart;

class Item extends baseModel {
    use ItemTraits;
    
    static $storeIdScope = false;

    protected $fillable = ['delta', 'currentQuantity', 'defaultPrice', 'notes'];
    protected $hidden = ['itemable_id', 'itemable_type'];
    
    static $indexAppends = [
        'totalProfitPrice',

        'averageBuyPricePerItem',
        'averageSellPricePerItem',
        'averageProfitPricePerItem',

        'averageTotalProfitPrice',
        'currentQuantityBuyWorth',
        'currentQuantitySellWorth',
        'expectedCurrentQuantityProfitPrice',
        'expectedTotalProfitPrice',
        'requiredMinimumPrice',

        'isPhone'
    ];

    public function itemable() {
        return $this->morphTo();
    }
    
    public function Carts() {
        return $this->hasMany(Cart::class);
    }

    public function getIsPhoneAttribute() {
        return $this->itemable->isPhone;
    }

    public function transactionDestroyed($Quantity, $costPerItem, $isBuy) {
        return $this->transactionPerformed($Quantity, $costPerItem, $isBuy, true);
    }

    public function transactionPerformed($Quantity, $costPerItem, $isBuy, $destroyed = false) {
        if (($isBuy === $destroyed) && ($this->currentQuantity < $Quantity)) return false;
        $coefficient = $destroyed ? -1 : 1;

        if ($isBuy) return $this->buyPerformed($Quantity, $costPerItem, $coefficient);
        return $this->sellPerformed($Quantity, $costPerItem, $coefficient);
    }
    
    public function buyPerformed($Quantity, $costPerItem, $coefficient) {
        $this->currentQuantity += $Quantity * $coefficient;
        $this->totalBuys += $Quantity * $coefficient;
        $this->totalBuyCost += $Quantity * $costPerItem * $coefficient;

        return $this->save();
    }

    public function sellPerformed($Quantity, $costPerItem, $coefficient) {
        $this->currentQuantity -= $Quantity * $coefficient;
        $this->totalSells += $Quantity * $coefficient;
        $this->totalSellCost += $Quantity * $costPerItem * $coefficient;

        return $this->save();
    }
}