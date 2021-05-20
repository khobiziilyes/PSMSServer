<?php 

namespace App\ModelFilters;

use App\ModelFilters\baseFilter;
use App\Models\Phone;
use App\Models\Accessory;

class ItemFilter extends baseFilter {
    protected $morphRrelations = [Phone::class, Accessory::class];

    public function name($name) {
    	return $this->filterByMorph($this->morphRrelations, 'name', $name);
    }

    public function brand($brand) {
    	return $this->filterByMorph($this->morphRrelations, 'brand', $brand);
    }

    public function currentQuantity($currentQuantity) {
    	return $this->where('currentQuantity', '>=', $currentQuantity);
    }

    public function delta($delta) {
    	return $this->where('delta', $delta);
    }
    
    public function isPhone($isPhone) {
    	return $this->whereHasMorph('itemable', [$isPhone ? Phone::class : Accessory::class]);
    }
}