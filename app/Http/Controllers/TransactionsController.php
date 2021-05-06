<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\baseController;

use App\Models\Customer;
use App\Models\Vendor;

use App\Models\Buy;
use App\Models\Sell;

use App\Models\Transaction;
use App\Models\Item;

class TransactionsController extends baseController {
    protected $theClass = Transaction::class;
    
    public function indexQuery() {
        $query = $this->theClass::query();
        if (request()->query->has('withTrashed')) $query = $query->withTrashed();

        return $query;
    }
    
    function getValidationRules($normalText, $isUpdate, $isBuy) {
        $validationRules = [
            'person_id' => 'required|exists:people,id,isVendor,' . ($isBuy ? '1' : '0'),
            'cart' => 'required|array|min:1',
            'cart.*.costPerItem' => 'required|integer',
            'cart.*.item_id' => 'required|exists:items,id|distinct:strict',
            'cart.*.Quantity' => 'required|integer|min:1',
            'notes' => $normalText
        ];

        return $validationRules;
    }

    public function store(Request $request) {
        $normalText = config('app.normalText');
        $isBuy = $this->theClass::$isBuy;

        $valArr = $this->getValidationRules($normalText, false, $isBuy);

        $validatedData = Validator::make(request()->input(), $valArr)->validate();

        $theInstance = new $this->theClass([
            'person_id' => $validatedData['person_id'],
            'notes' => $validatedData['notes'] ?? null
        ]);
        
        DB::transaction(function () use($theInstance, $validatedData, $isBuy) {
            $theInstance->save();

            foreach ($validatedData['cart'] as $i => $cart_item) {
                $Item = Item::findOrFail($cart_item['item_id']);
                
                if (!$isBuy && ($Item->currentQuantity < $cart_item['Quantity'])) 
                    throw ValidationException::withMessages(["cart.$i.Quantity" => 'This quantity is not available.']);

                $priceChanged = $isBuy ? null : ($cart_item['costPerItem'] !== $Item->defaultPrice);
                $Item->transactionPerformed($cart_item['Quantity'], $cart_item['costPerItem'], $isBuy);

                $theInstance->Carts()->create([
                    'item_id' => $cart_item['item_id'],
                    'Quantity' => $cart_item['Quantity'],
                    'costPerItem' => $cart_item['costPerItem'],
                    'priceChanged' => $priceChanged
                ]);
            }
        });

        return $theInstance;
    }

    public function destroy($id) {
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

class BuyController extends TransactionsController { public $theClass = Buy::class; }
class SellController extends TransactionsController { public $theClass = Sell::class; }