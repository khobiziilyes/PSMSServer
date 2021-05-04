<?php

namespace App\Models;

class Item extends baseModel {
    protected $fillable = ['delta', 'currentQuantity', 'defaultPrice', 'notes'];
    protected $appendWith = ['itemable:id,name,brand'];
    
    protected $appendAppends = [
        'isPhone',
        'mediumSellPrice',
        'mediumBuyPrice',
        'totalProfitPrice',
        'mediumProfitPrice',
        'expectedTotalProfitPrice'
    ];

    protected $appendHidden = ['itemable_id', 'itemable_type'];

    public function Itemable() {
        return $this->morphTo();
    }
    
    public function getIsPhoneAttribute() {
        return $this->Itemable->isPhone;
    }

    public function getMediumSellPriceAttribute() {
        return $this->generalStatistic('totalSellCost', 'totalSells');
    }

    public function getMediumBuyPriceAttribute() {
        return $this->generalStatistic('totalBuyCost', 'totalBuys');
    }

    public function getTotalProfitPriceAttribute() {
        return $this->totalSellCost - $this->totalBuyCost;
    }

    public function getMediumProfitPriceAttribute() {
        return $this->generalStatistic('totalProfitPrice', 'totalSells');
    }

    public function getExpectedTotalProfitPriceAttribute() {
        return ($this->mediumProfitPrice * $this->currentQuantity) + $this->totalProfitPrice;
    }

    public function generalStatistic($First, $Second) {
        if (is_null($this->$First) || is_null($this->$Second)) return 0;
        if ($this->$Second === 0) return 0;
        return floor($this->$First / $this->$Second);
    }
    
    public function transactionPerformed($Quantity, $costPerItem, $isBuy, $destroyed = false) {
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
        if (($Quantity < 1) || ($this->currentQuantity < $Quantity)) return false;

        $this->currentQuantity -= $Quantity * $coefficient;
        $this->totalSells += $Quantity * $coefficient;
        $this->totalSellCost += $Quantity * $costPerItem * $coefficient;

        return $this->save();
    }
}