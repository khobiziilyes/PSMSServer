<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\baseController;

use App\Models\Transaction;
use App\Models\Buy;
use App\Models\Sell;
use App\Models\imei;

class TransactionsController extends baseController {
    public $theClass = Transaction::class;

    function getValidationRules($normalText) {
        return [
            'costPerItem' => 'required|integer',
            'Quantity' => 'required|integer' . ($this->theClass::$isBuy ? '' : '|between:1,'),
            'person_id' => ['required', 
                Rule::exists('people', 'id')->where(function ($query) {
                    return $query->where('isVendor', $this->theClass::$isBuy);
                })    
            ],
            'item_id' => 'required|exists:items,id',
            'notes' => $normalText
        ];
    }

    public function store(Request $request) {
        $theInstance = $this->createOrUpdate($request->input(), null, false);
        $theInstance->priceChanged = $this->theClass::$isBuy ? null : ($theInstance->costPerItem !== $theInstance->Item->defaultPrice);

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
                    $this->imeiExtraValidation
                ]
            ];

            Validator::make($theDatas, $IMEIRules)->validate();
            $theInstance->save();

            foreach ($request->input('imei') as $imei) $this->saveIMEI($imei, $theInstance);
        } else {
            $theInstance->save();
        }

          
        return $theInstance;
    }
}

class BuyController extends TransactionsController {
    public $theClass = Buy::class;
    protected $imeiExtraValidation = 'unique:imei,imei';
 
    function saveIMEI($imei, $theInstance) {
        $theDatas = [
            'item_id' => $theInstance->item_id,
            'imei' => $imei,
            'buy_payment_id' => $theInstance->id,
            'sell_payment_id' => null,
            'notes' => null
        ];

        $imeiInstance = new imei($theDatas);
        $imeiInstance->save();
    }
}

class SellController extends TransactionsController {
    public $theClass = Sell::class;
    protected $imeiExtraValidation = 'exists:imei,imei';
 
    function saveIMEI($imei, $theInstance) {
        $imeiInstance = imei::where('imei', $imei)->firstOrFail();
        $imeiInstance->sell_payment_id = $theInstance->id;

        $imeiInstance->save();
    }
}