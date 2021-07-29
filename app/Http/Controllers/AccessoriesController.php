<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductsController;
use App\Models\Accessory;

class AccessoriesController extends ProductsController {
    protected $theClass = Accessory::class;
    
    public function allowedFilters() {
        return $this->_allowedFilters(['type']);
    }
    
    public function getValidationRules($resource_id) {
    	return $this->_getValidationRules($resource_id, [
            'brand' => 'present|name',
            'type_id' => 'required|numeric|between:1,18'
        ]);
    }

    /*
        0, 'Others'
        1, 'Shock Proof'
        2, 'Glass'
        3, 'Pouch'
        4, 'Charger Cable'
        5, 'Charger Box'
        6, 'Full Charger'
        7, 'Kit-man'
        8, 'Bluetooth'

        9, 'Memory Card'
        10, 'Flash Disk'
        11, 'Power Bank'
        12, 'Auto-Accessories'
        13, 'Ring'
        14, 'Selfie'
        15, 'Adapter'
        16, 'Casque'
        17, 'Baff'
    */
}