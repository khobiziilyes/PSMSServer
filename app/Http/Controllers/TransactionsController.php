<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\baseController;

use App\Models\Customer;
use App\Models\Vendor;

use App\Models\Buy;
use App\Models\Sell;

use App\Models\Transaction;
use App\Models\Item;

class TransactionsController extends baseController {
    public $theClass = Transaction::class;

    function getValidationRules($normalText, $isBuy) {
        $validationRules = [
            'person_id' => 'required|exists:people,id,isVendor,' . ($isBuy ? '1' : '0'),
            'cart' => 'required|array|min:1',
            'cart.*.costPerItem' => 'required|integer',
            'cart.*.item_id' => 'required|exists:items,id|distinct:strict',
            'cart.*.Quantity' => 'required|integer|min:1',
            'notes' => $normalText
        ];

        if (!$isBuy) $validationRules['cart.*'] = function($attribute, $value, $fail) {  
            $item_id = $value['item_id'] ?? null;
            $Quantity = $value['Quantity'] ?? null;
                
            if (is_null($item_id) || is_null($Quantity)) return $fail('Cart is invalid.');
            if (Item::findOrFail($item_id)->currentQuantity < $Quantity) return $fail("$Quantity item aren't available.");
        };

        return $validationRules;
    }

    public function store(Request $request) {
        $normalText = config('app.normalText');
        $isBuy = $this->theClass::$isBuy;

        $valArr = $this->getValidationRules($normalText, $isBuy);

        $validatedData = Validator::make(request()->input(), $valArr)->validate();

        $newCart = [];
        foreach ($validatedData['cart'] as $cart_item) {
            $Item = Item::findOrFail($cart_item['item_id']);
            
            $cart_item['priceChanged'] = $isBuy ? null : ($cart_item['costPerItem'] !== $Item->defaultPrice);            
            $Item->transactionPerformed($cart_item['Quantity'], $cart_item['costPerItem'], $isBuy);

            $newCart[] = $cart_item;
        }

        $theInstance = ($isBuy ? Buy::class : Sell::class)::create([
            'person_id' => $validatedData['person_id'],
            'notes' => $validatedData['notes'] ?? null,
            'cart' => $newCart
        ]);

        $theInstance->save();

        return $theInstance;
    }
}

class BuyController extends TransactionsController {
    public $theClass = Buy::class;

    public function destroyBuy(Buy $Transaction) {
        ret
    }
}
class SellController extends TransactionsController {
    public $theClass = Sell::class;

    public function destroySell(Sell $Transaction) {
        
    }
}