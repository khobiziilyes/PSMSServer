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
    
    public function allowedFilters() {
        return ['name', 'brand', 'type'];
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

    public function indexQuery($request) {
        return Accessory::with('Phones:phone_id,name,brand');
    }

    /*
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
        18, 'Others'
    */
}