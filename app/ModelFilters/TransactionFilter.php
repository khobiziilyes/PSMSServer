<?php 

namespace App\ModelFilters;

use App\ModelFilters\baseFilter;
use App\Models\Phone;
use App\Models\Accessory;

class TransactionFilter extends baseFilter {
	protected $morphRelations = [Phone::class, Accessory::class];

    public function personId($person) {
    	return $this->where('person_id', $person);
    }

    public function productName($name) {
    	return $this->whereHas('Carts', function($query) use ($name){
    		$query->whereHas('Item', function($query2) use($name) {
    			$query2->filterByMorph($this->morphRelations, 'name', $name);
    		});
    	});
    }

    public function itemId($item_id) {
    	return $this->whereHas('Carts', function($query) use ($item_id){
    		$query->whereHas('Item', function($query2) use($item_id) {
    			$query2->where('itemable_id', $item_id);
    		});
    	});
    }

    public function productId($product_id) {
    	return $this->whereHas('Carts', function($query) use ($product_id){
    		$query->whereHas('Item', function($query2) use($product_id) {
    			$query2->where('itemable_id', $product_id);
    		});
    	});
    }

    public function isPhone($isPhone) {
        return $this->whereHas('Carts', function($query) use ($isPhone) {
    		$query->whereHas('Item', function($query2) use($isPhone) {
    			$query2->whereHasMorph('itemable', [$isPhone ? Phone::class : Accessory::class]);
    		});
    	});
    }
}