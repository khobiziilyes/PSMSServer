<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ControllersTraits;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Phone;
use App\Models\Accessory;

class ItemsController extends baseController {
    use ControllersTraits\destroyModel;
    
    protected $theClass = Item::class;
    protected $beforeDestroy = 'Carts';
    
    public function allowedFilters() {
        return ['name', 'brand', 'search', 'isPhone', 'currentQuantity', 'delta'];
    }

    function getValidationRules($isUpdate, $itemable_id = null, $isPhone) {
        return [
            'defaultPrice' => 'required|integer|min:0',
            'delta' => ['required', 'integer', 'between:-3,3', Rule::unique('items')->where(function ($query) use ($itemable_id, $isPhone) {
                return $query->where('itemable_id', $itemable_id)
                    ->where('itemable_type', $isPhone ? Phone::class : Accessory::class);
            })],
            'currentQuantity' => 'required|integer|min:0',
            'notes' => 'notes'
        ];
    }

    public function update(Request $request, Item $item) {
        $this->authorizeAction('Update');
        if ($request->input('defaultPrice') !== $item->defaultPrice) \Bouncer::authorize('changeSellPrice');

        return $this->storeOrUpdate($request->input(), $item->id);
    }

    public function storeItemable($type, $Itemable) {
        $this->authorizeAction('Write');
        $Itemable = ($type === 'phone' ? Phone::class : Accessory::class)::findOrFail($Itemable);
        
        $valArr = $this->getValidationRules(false, $Itemable->id, $Itemable->isPhone);
        $validatedData = Validator::make(request()->input(), $valArr)->validate();
        
        $theInstance = $Itemable->Items()->create($validatedData);
        $theInstance->save();
        
        $totalRows = Item::count();
        
        return [
            'data' => $theInstance,
            'totalRows' => $totalRows
        ];
    }

    function formatData($collection, $request) {
        $collection->map(function($item) {
            $item->append(Item::$indexAppends);
        });
    }
}