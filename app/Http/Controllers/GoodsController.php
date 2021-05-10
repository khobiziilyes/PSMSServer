<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PhonesScrapController;
use App\Http\Controllers\baseController;
use Illuminate\Http\Request;
use App\Models\Phone;
use App\Models\Accessory;

class AccessoriesController extends baseController {
    protected $beforeDestroy = 'items';
    protected $theClass = Accessory::class;

    function getValidationRules($isUpdate) {
    	$requiredName = ($isUpdate ? '' : 'required|') . 'name';

        $baseRules = [
            'name' => $requiredName,
            'brand' => $requiredName,
            'notes' => 'notes'
        ];

        if (!$isUpdate) $baseRules['type_id'] = 'required|numeric|between:1,5';

        return $baseRules;
    }
}

class PhonesController extends PhonesScrapController {
    protected $theClass = Phone::class;

    public function search(Request $request) {
        $validatedData = Validator::make($request->input(), [
            'query' => 'required|regex:/^[\w\d ]+$/'
        ])->validate();

        $query = $validatedData['query'];
        $devicesDB = Phone::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%']);
        
        if ($devicesDB->count() === 0) {
            $devices = $this->searchDevices($query);
            if (count($devices) === 0) return [];

            foreach ($devices as $device) 
                Phone::create(array_merge($device, ['type_id' => 0]))->save();
        }
        
        return $devicesDB->get();
    }
}
