<?php 

namespace App\ModelFilters;

use App\ModelFilters\baseFilter;
use App\Models\Phone;
use App\Models\Accessory;

class ItemFilter extends baseFilter {
    protected $morphRelations = [Accessory::class, Phone::class];

    public function search($term) {
        return $this->whereHasMorph('itemable', $this->morphRelations, function($query) use($term) {
            return $query->whereLike('name', $term)->whereLike('brand', $term, 'or');
        });
    }

    public function name($name) {
    	return $this->filterByMorph('itemable', $this->morphRelations, 'name', $name);
    }

    public function brand($brand) {
    	return $this->filterByMorph('itemable', $this->morphRelations, 'brand', $brand);
    }

    public function currentQuantity($currentQuantity) {
    	return $this->where('currentQuantity', '>=', $currentQuantity);
    }

    public function delta($delta) {
    	return $this->where('delta', $delta);
    }
    
    public function isPhone($isPhone) {
        return $this->whereHasMorph('itemable', [$this->morphRelations[(int) $isPhone]]);
    }
}