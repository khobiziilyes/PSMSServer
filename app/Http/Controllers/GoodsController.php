<?php

namespace App\Http\Controllers;

use App\Http\Controllers\baseController;
use App\Models\Phone;
use App\Models\Accessory;

class GoodsController extends baseController {
    protected $beforeDestroy = 'items';

    function getValidationRules($normalText) {
        return [
            'name' => $normalText,
            'brand' => $normalText,
            'notes' => $normalText,
            'type_id' => 'required|numeric|' . ($this->theClass::$isPhone ? '' : 'not_') . 'in:0' // real Validation.
        ];
    }
}

class PhonesController extends GoodsController { protected $theClass = Phone::class; }
class AccessoriesController extends GoodsController { protected $theClass = Accessory::class; }