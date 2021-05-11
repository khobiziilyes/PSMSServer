<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PhonesScrapController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Phone;

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
                new Phone(array_merge($device, ['type_id' => 0]));
        }
        
        return $devicesDB->get();
    }
}
