<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ControllersTraits;

use App\Http\Controllers\baseController;
use App\Models\Accessory;

class AccessoriesController extends baseController {
    use ControllersTraits\storeModel;
    use ControllersTraits\updateModel;
    use ControllersTraits\destroyModel;

    protected $beforeDestroy = 'items';
    protected $theClass = Accessory::class;
    protected $modelName = 'accessories';
    
    public function allowedFilters() {
        return ['name', 'brand', 'search', 'type'];
    }
    
    function getValidationRules($isUpdate) {
    	$requiredName = ($isUpdate ? '' : 'required|') . 'name';

        $baseRules = [
            'name' => $requiredName,
            'brand' => $requiredName,
            'notes' => 'notes'
        ];

        if (!$isUpdate) $baseRules['type_id'] = 'required|numeric|between:1,18';

        return $baseRules;
    }

    /*
        1, 'Shock Proof'
        2, 'Pouch'
        3, 'Glass'
        4, 'Charger Cable'
        5, 'Charger Box'
        6, 'Full Charger'
        7, 'Memory Card'
        8, 'Flash Disk'
        9, 'Kit-man'
        10, 'Bluetooth'
        11, 'Power Bank'
        12, 'Auto-Accessories'
        13, 'Ringes'
        14, 'Selfie'
        15, 'Adapter'
        16, 'Casque'
        17, 'Baff'
        18, 'Others'
    */
}