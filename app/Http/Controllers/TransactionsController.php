<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\baseController;

use App\Models\Customer;
use App\Models\Vendor;

use App\Models\Transaction;
use App\Models\Item;
use App\Models\Cart;

use App\Models\Phone;
use App\Models\Accessory;

class TransactionsController extends baseController {
    protected $theClass = Transaction::class;
    protected $withTrashed = true;
    protected $modelName = 'transaction';
    
    public function indexQuery() {
        return $this->theClass::with(['Carts']);
    }
    
    function getValidationRules($isUpdate, $isBuy) {
        $validationRules = [
            'person_id' => 'required|exists:people,id,isVendor,' . ($isBuy ? '1' : '0'),
            'cart' => 'required|array|min:1',
            'cart.*.0' => 'required|exists:items,id|distinct:strict',
            'cart.*.1.*.0' => 'required|integer|min:0',
            'cart.*.1.*.1' => 'required|integer|min:1',
            'notes' => 'notes'
        ];

        return $validationRules;
    }

    public function store(Request $request) {
        //Gate::authorize('can', ['C', $this->modelName]);

        $isBuy = $request->has('isBuy');

        $valArr = $this->getValidationRules(false, $isBuy);
        $validatedData = Validator::make(request()->input(), $valArr)->validate();

        $theInstance = new $this->theClass(Arr::except($validatedData, ['cart']));
        $theInstance->isBuy = $isBuy;

        DB::transaction(function () use($theInstance, $validatedData, $isBuy) {
            $theInstance->save();

            foreach ($validatedData['cart'] as $cart_item_group) {
                $Item = Item::findOrFail($cart_item_group[0]);
                $carts_item = $cart_item_group[1];
                
                $totalQuantity = array_sum(array_map(function($Arr) {
                    return $Arr[1];
                }, $carts_item));
                
                if (!$isBuy && ($Item->currentQuantity < $totalQuantity)) 
                    throw ValidationException::withMessages(["Quantity" => 'This quantity is not available.']);

                foreach ($carts_item as $cart_item) {
                    $priceChanged = $isBuy ? null : ($cart_item[0] !== $Item->defaultPrice);
                    
                    $Item->transactionPerformed($cart_item[1], $cart_item[0], $isBuy);

                    $theInstance->Carts()->create([
                        'item_id' => $Item->id,
                        'Quantity' => $cart_item[1],
                        'costPerItem' => $cart_item[0],
                        'priceChanged' => $priceChanged
                    ]);
                }
            }
        });

        return $theInstance;
    }

    public function destroy($id) {
        //Gate::authorize('can', ['D', $this->modelName]);

        $Transaction = $this->theClass::findOrFail($id);

        DB::transaction(function () use($Transaction) {
            $isBuy = $Transaction->isBuy;

            $Transaction->Carts->each(function($Cart) use ($isBuy) {
                $Item = $Cart->Item;
                $itemCalcsDestroyed = $Item->transactionDestroyed($Cart->Quantity, $Cart->costPerItem, $isBuy);

                if (!$itemCalcsDestroyed)
                    throw ValidationException::withMessages(["Quantity" => 'This quantity is not available.']);
            });

            $Transaction->delete();
        });

        return ['deleted' => true];
    }
}