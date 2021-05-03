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
use App\Models\imei;

class TransactionsController extends baseController {
    public $theClass = Transaction::class;

    function getValidationRules($normalText, $maxQuantity) {
        return [
            'costPerItem' => 'required|integer',
            'Quantity' => 'required|integer|min:1' . (is_null($maxQuantity) ? '' : ('|max:' . $maxQuantity)),
            'notes' => $normalText
        ];
    }

    public function storeTransaction(Item $Item, $Person) {
        $normalText = config('app.normalText');

        $valArr = $this->getValidationRules($normalText, $Item->currentQuantity);
        $validatedData = Validator::make(request()->input(), $valArr)->validate();
        
        $theInstance = $Item->Transactions()->create($validatedData);
        $theInstance->priceChanged = $this->theClass::$isBuy ? null : ($theInstance->costPerItem !== $theInstance->Item->defaultPrice);
        
        $theInstance->save();

        if ($theInstance->Item->isPhone) {
            $theDatas = ['imei' => $request->input('imei', [])];

            $IMEIRules = [
                'imei' => [
                    'required',
                    'array',
                    'size:' . $theInstance->Quantity
                ],
                'imei.*' => [
                    'imei',
                    'distinct:strict',
                    $theInstance->isBuy ? 'unique:imei,imei' : 'exists:imei,imei,sell_payment_id,NULL'
                ]
            ];

            $theDatas = Validator::make($theDatas, $IMEIRules)->after(function($validator) {
                if ($this->somethingElseIsInvalid()) $theInstance->delete();
            })->validate();
            
            dd($theDatas);

            foreach ($theDatas as $imei) $this->saveIMEI($imei, $theInstance);
        }

        $Item->changeQuantityBy($theInstance->Quantity * ($theInstance->isBuy ? 1 : -1));

        return $theInstance;
    }
}

class BuyController extends TransactionsController {
    public $theClass = Buy::class;
    
    function storeBuy(Item $Item, Vendor $Vendor) {
        return $this->storeTransaction($Item, $Vendor);
    }

    function saveIMEI($imei, $theInstance) {
        $imeiInstance = imei::create([
            'item_id' => $theInstance->item_id,
            'imei' => $imei,
            'buy_payment_id' => $theInstance->id,
            'sell_payment_id' => null
        ]);

        $imeiInstance->save();
    }
}

class SellController extends TransactionsController {
    public $theClass = Sell::class;
    
    function storeSell(Item $Item, Customer $Customer) {
        return $this->storeTransaction($Item, $Customer);
    }
    
    function saveIMEI($imei, $theInstance) {
        $imeiInstance = imei::where('imei', $imei)->firstOrFail();
        $imeiInstance->sell_payment_id = $theInstance->id;

        $imeiInstance->save();
    }
}