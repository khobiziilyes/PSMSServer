<?php

namespace App\Models;

trait ItemTraits {
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
}