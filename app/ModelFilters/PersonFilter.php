<?php 

namespace App\ModelFilters;

use App\ModelFilters\baseFilter;

class PersonFilter extends baseFilter {
	public function name($name) {
    	return $this->whereLike('name', $name);
    }
}