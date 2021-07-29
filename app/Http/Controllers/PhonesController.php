<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductsController;
use App\Models\Phone;

class PhonesController extends ProductsController {
    protected $theClass = Phone::class;
    
    public function allowedFilters() {
        return $this->_allowedFilters();
    }

    public function getValidationRules($resource_id) {
    	return $this->_getValidationRules($resource_id, [
    		'brand' => 'required|name',
    	]);
    }
}