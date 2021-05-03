<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Phone;
use App\Models\Accessory;

class ItemsController extends baseController {
    protected $theClass = Item::class;
    protected $beforeDestroy = 'Transactions';

    function getValidationRules($normalText, $itemable_id = null) {
        $validationRules = [
            'defaultPrice' => 'required|integer',
            'notes' => $normalText
        ];
        
        if (!is_null($itemable_id)) {
            $validationRules['delta'] = 'required|integer|between:-3,3|unique:items,delta,NULL,id,itemable_id,' . $itemable_id;
            $validationRules['currentQuantity'] = 'required|integer';
        }
    
        return $validationRules;
    }

    public function storePhoneItem(Phone $Itemable) { return $this->storeItemable($Itemable); }
    public function storeAccessoryItem(Accessory $Itemable) { return $this->storeItemable($Itemable); }

    public function storeItemable($Itemable) {
        $normalText = config('app.normalText');

        $valArr = $this->getValidationRules($normalText, $Itemable->id);
        $validatedData = Validator::make(request()->input(), $valArr)->validate();
        
        $theInstance = $Itemable->Items()->create($validatedData);
        $theInstance->save();
        
        return $theInstance;
    }
}