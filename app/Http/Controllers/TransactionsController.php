<?php

namespace App\Http\Controllers;

use App\Http\Resources\Response;

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
    
    public function isBuy($request) {  
        $actions = $request->route()->getAction();
        $isBuy = $actions['isBuy'] ?? null;

        if (is_null($isBuy)) abort(404);

        return $isBuy;
    }

    public function index(Request $request) {
        $this->authorizeAction('Read', $this->isBuy($request) ? 'Buy' : 'Sell');
        
        $query = $this->indexQuery($request);
        if ($request->query->has('withTrashed')) $query->withTrashed();
        
        return $this->paginateQuery($query, $request);
    }

    public function allowedFilters() {
        return ['isBuy', 'isPhone', 'personId', 'productName', 'itemId', 'productId'];
    }
    
    public function indexQuery($request) {
        return $this->theClass::with(['Carts'])->where('isBuy', $this->isBuy($request));
    }
    
    function getValidationRules($isBuy) {
        $validationRules = [
            'person_id' => 'required|exists:people,id,isVendor,' . ($isBuy ? '1' : '0'),
            'cart' => 'required|array|min:1',
            'cart.*.item_id' => 'required|exists:items,id|distinct:strict',
            'cart.*.list.*.costPerItem' => 'required|integer|min:0',
            'cart.*.list.*.Quantity' => 'required|integer|min:1',
            'notes' => 'present|notes'
        ];

        return $validationRules;
    }

    public function store(Request $request) {
        $isBuy = $this->isBuy($request);
        
        $this->authorizeAction('Write', $isBuy ? 'Buy' : 'Sell');

        $valArr = $this->getValidationRules($isBuy);
        $validatedData = Validator::make(request()->input(), $valArr)->validate();

        $theInstance = new $this->theClass(Arr::except($validatedData, ['cart']));
        $theInstance->isBuy = $isBuy;

        DB::transaction(function () use($theInstance, $validatedData, $isBuy) {
            $theInstance->save();

            foreach ($validatedData['cart'] as $cart_item_group) {
                $Item = Item::findOrFail($cart_item_group['item_id']);
                $carts_item = $cart_item_group['list'];
                
                $totalQuantity = array_sum(array_map(function($Arr) {
                    return $Arr['Quantity'];
                }, $carts_item));
                
                if (!$isBuy && ($Item->currentQuantity < $totalQuantity)) // Fix to fit both sell & buy
                    throw ValidationException::withMessages(["Quantity" => 'This quantity is not available.']);

                foreach ($carts_item as $cart_item) {
                    $cart_item_Quantity = $cart_item['Quantity'];
                    $cart_item_costPerItem = $cart_item['costPerItem'];

                    $priceChanged = $isBuy ? null : ($cart_item_costPerItem !== $Item->defaultPrice);
                    
                    $Item->transactionPerformed($cart_item_Quantity, $cart_item_costPerItem, $isBuy);

                    $theInstance->Carts()->create([
                        'item_id' => $Item->id,
                        'Quantity' => $cart_item_Quantity,
                        'costPerItem' => $cart_item_costPerItem,
                        'priceChanged' => $priceChanged
                    ]);
                }
            }
        });

        return $this->instanceResponse($request, $theInstance);
    }

    public function destroy($id) {
        $Transaction = $this->theClass::findOrFail($id);
        $isBuy = $Transaction->isBuy;

        $this->authorizeAction('Update', $isBuy ? 'Buy' : 'Sell');
        
        DB::transaction(function () use($Transaction, $isBuy) {
            

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

    public function formatOutput($collection, $request) {
        if (!$this->isBuy($request)) $collection->map(function ($transaction) {
            $transaction->Carts->append(['profitPerItem', 'totalProfit']);
            $transaction->append('Profit');
        });
    }
}