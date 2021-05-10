<?php

namespace App\Http\Controllers;

use App\Http\Controllers\baseController;
use App\Models\Vendor;
use App\Models\Customer;

class PeopleController extends baseController {
    protected $beforeDestroy = 'transactions';

    function getValidationRules($isUpdate) {
        $phoneRegex = '/^0[567]\d{8}$/';        
        $required = $isUpdate ? '' : 'required|';

        return [
            'name' => $required . 'name',
            'phone1' => $required . "regex:$phoneRegex",  

            'address' => 'notes',
            'phone2' => "regex:$phoneRegex",
            'fax' => 'regex:/^0\d{8}$/',
            'notes' => 'notes'
        ];
    }
}

class CustomersController extends PeopleController { protected $theClass = Customer::class; }
class VendorsController extends PeopleController { protected $theClass = Vendor::class; }