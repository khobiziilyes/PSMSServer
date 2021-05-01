<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemsController extends baseController {
    protected $theClass = Item::class;
    protected $beforeDestroy = 'Transactions';

    function getValidationRules($normalText) {
        return [
            'good_id' => 'required|exists:goods,id',
            'delta' => 'required|unique:items,delta,NULL,id,good_id,' . request()->good_id,
            'currentQuantity' => 'required|numeric',
            'defaultPrice' => 'required|numeric',
            'notes' => $normalText
        ];
    }
}