<?php 

namespace App\ModelFilters;

use App\ModelFilters\baseFilter;

class ProductFilter extends baseFilter {
	public function search($term) {
        return $this
            ->whereLike('name', $term)
            ->whereLike('brand', $term, 'or');
    } 

    public function name($name) {
    	return $this->whereLike('name', $name);
    }

    public function brand($brand) {
    	return $this->whereLike('brand', $brand);
    }

    public function type($type_id) {
    	return $this->where('type_id', $type_id);
    }
}