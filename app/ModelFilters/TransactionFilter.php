<?php 

namespace App\ModelFilters;

use Illuminate\Support\Carbon;
use App\ModelFilters\baseFilter;
use App\Models\Phone;
use App\Models\Accessory;

class TransactionFilter extends baseFilter {
	protected $morphRelations = [Phone::class, Accessory::class];

    public function person($person) {
    	return $this->where('person_id', $person);
    }

    public function name($name) {
    	return $this->whereHas('Carts', function($query) use ($name){
    		$query->whereHas('Item', function($query2) use($name) {
    			$query2->filterByMorph($this->morphRelations, 'name', $name);
    		});
    	});
    }

    public function item($item_id) {
    	return $this->whereHas('Carts', function($query) use ($item_id){
    		$query->whereHas('Item', function($query2) use($item_id) {
    			$query2->where('itemable_id', $item_id);
    		});
    	});
    }

    public function good($good_id) {
    	return $this->whereHas('Carts', function($query) use ($good_id){
    		$query->whereHas('Item', function($query2) use($good_id) {
    			$query2->where('itemable_id', $good_id);
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