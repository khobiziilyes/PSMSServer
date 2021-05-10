<?php

namespace App\Http\Controllers;

use App\Http\Controllers\baseController;
use App\Models\Phone;
use App\Models\Accessory;

class GoodsController extends baseController {
    protected $beforeDestroy = 'items';

    function getValidationRules($isUpdate) {
    	$requiredName = ($isUpdate ? '' : 'required|') . 'name';

        $baseRules = [
            'name' => $requiredName,
            'brand' => $requiredName,
            'notes' => 'notes'
        ];

        if (!$isUpdate) $baseRules['type_id'] = 'required|numeric|' . ($this->theClass::$isPhone ? '' : 'not_') . 'in:0';

        return $baseRules;
    }
}

class PhonesController extends GoodsController { protected $theClass = Phone::class; }
class AccessoriesController extends GoodsController { protected $theClass = Accessory::class; }