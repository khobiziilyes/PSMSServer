<?php

namespace App\Models;

use App\Models\Cart;

class Item extends baseModel {
    protected $fillable = ['delta', 'currentQuantity', 'defaultPrice', 'notes'];
    protected $with = ['itemable:id,name,brand'];
    protected $_hidden = ['itemable_id', 'itemable_type'];
    static $indexAppends = [
        'totalProfitPrice',

        'averageBuyPricePerItem',
        'averageSellPricePerItem',
        'averageProfitPricePerItem',

        'averageTotalProfitPrice',
        'currentQuantityBuyWorth',
        'currentQuantitySellWorth',
        'ExpectedCurrentQuantityProfitPrice',
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

    public function getTotalProfitPriceAttribute() {
        return $this->totalSellCost - $this->totalBuyCost;
    }

    public function getAverageBuyPricePerItemAttribute() {
        return $this->generalStatistic('totalBuyCost', 'totalBuys');
    }

    public function getAverageSellPricePerItemAttribute() {
        return $this->generalStatistic('totalSellCost', 'totalSells');
    }

    public function getAverageProfitPricePerItemAttribute() {
        return $this->averageSellPricePerItem - $this->averageBuyPricePerItem;
    }

    public function getAverageTotalProfitPriceAttribute() {
        return $this->averageProfitPricePerItem * $this->totalSells;
    }

    public function getCurrentQuantityBuyWorthAttribute() {
        return $this->averageBuyPricePerItem * $this->currentQuantity;
    }

    public function getCurrentQuantitySellWorthAttribute() {
        return $this->averageSellPricePerItem * $this->currentQuantity;
    }

    public function getExpectedCurrentQuantityProfitPriceAttribute() {
        return $this->averageProfitPricePerItem * $this->currentQuantity;
    }

    public function getExpectedTotalProfitPriceAttribute() {
        return $this->averageProfitPrice + $this->ExpectedCurrentQuantityProfitPrice;
    }

    public function getRequiredMinimumPriceAttribute() {
        return $this->generalStatistic('totalProfitPrice', 'currentQuantity') * -1;
    }

    public function generalStatistic($First, $Second) {
        if (is_null($this->$First) || is_null($this->$Second)) return 0;
        if ($this->$Second === 0) return 0;

        $val = $this->$First / $this->$Second;
        $val = floor($val);
        $val /= 10;
        $val = floor($val);
        $val *= 10;

        return $val;
    }
    
    // Those are transactions statistics functions

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