<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ControllersTraits;

use Bouncer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\Item;
use App\Models\Phone;
use App\Models\Accessory;

class ItemsController extends baseController {
    use ControllersTraits\destroyModel;
    use ControllersTraits\storeOrUpdateModel;
    
    protected $theClass = Item::class;
    protected $beforeDestroy = 'Carts';
    
    public function allowedFilters() {
        return ['name', 'brand', 'isPhone', 'currentQuantity', 'delta'];
    }

    public function indexQuery($request) {
        return Item::with('itemable:id,name,brand');
    }

    function getValidationRules($resource_id, $itemable_id = null, $isPhone = null) {
        $rules = [
            'defaultPrice' => 'required|integer|min:0',
            'currentQuantity' => 'required|integer|min:0',
            'notes' => 'present|notes'
        ];

        if (is_null($resource_id)) {
            $rules['delta'] = [
                'required',
                'integer',
                'between:-3,3',
                Rule::unique('items')->where(function ($query) use ($itemable_id, $isPhone) {
                    return $query->where('itemable_id', $itemable_id)
                        ->where('itemable_type', $isPhone ? Phone::class : Accessory::class);
                })
            ];
        }

        return $rules;
    }

    public function update(Request $request, Item $item) {
        $this->authorizeAction('Update');
        
        $valArr = $this->getValidationRules($item->id);
        $validatedData = Validator::make($request->input(), $valArr)->validate();
    
        if ($validatedData['defaultPrice'] !== $item->defaultPrice) Bouncer::authorize('canUpdateDefaultPrice');
        if ($validatedData['currentQuantity'] !== $item->currentQuantity) Bouncer::authorize('canUpdateCurrentQuantity');

        return $this->storeOrUpdate($validatedData, $item->id);
    }

    public function storeItemable($type, $Itemable) {
        $this->authorizeAction('Write');

        $request = request();

        $Itemable = ($type === 'phone' ? Phone::class : Accessory::class)::findOrFail($Itemable);
        
        $valArr = $this->getValidationRules(null, $Itemable->id, $Itemable->isPhone);
        $validatedData = Validator::make($request->input(), $valArr)->validate();
        
        $theInstance = $Itemable->Items()->create($validatedData);
        $theInstance->save();
        
        return $theInstance;
    }

    function formatOutput($collection, $request) {
        $collection->map(function($item) {
            $item->append(Item::$indexAppends);
        });
    }

    function finalOutput($paginator) {
        $collection = $paginator['data'];
        
        $Stats = array_merge(array_slice(Item::$indexAppends, 0, -1), [
            'totalBuyCost',
            'totalSellCost',
            'totalBuys',
            'totalSells'
        ]);

        $newCollection = array_map(function($item) use($Stats) {
            $newItem = ['Stats' => []];
            
            foreach ($item as $key => $value) {
                if (in_array($key, $Stats)) {
                    $newItem['Stats'][$key] = $value;
                } else {
                    $newItem[$key] = $value;
                }
            }

            return $newItem;
        }, $collection);

        return array_merge($paginator, ['data' => $newCollection]);
    }
}