<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Phone;
use App\Models\Accessory;

class ItemsController extends baseController {
    protected $theClass = Item::class;
    protected $beforeDestroy = 'Carts';

    function getValidationRules($normalText, $isUpdate, $itemable_id = null) {
        $validationRules = [
            'defaultPrice' => 'required|integer',
            'notes' => $normalText
        ];
        
        if (!$isUpdate) {
            $validationRules['delta'] = 'required|integer|between:-3,3|unique:items,delta,NULL,id,itemable_id,' . $itemable_id;
            $validationRules['currentQuantity'] = 'required|integer';
        }
    
        return $validationRules;
    }

    public function storeItemable($type, $Itemable) {
        $Itemable = ($type === 'phone' ? Phone::class : Accessory::class)::findOrFail($Itemable);
        $normalText = config('app.normalText');

        $valArr = $this->getValidationRules($normalText, false, $Itemable->id);
        $validatedData = Validator::make(request()->input(), $valArr)->validate();
        
        $theInstance = $Itemable->Items()->create($validatedData);
        $theInstance->save();
        
        return $theInstance;
    }
}