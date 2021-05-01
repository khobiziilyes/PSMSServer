<?php

namespace App\Http\Controllers;

use App\Http\Controllers\baseController;
use App\Models\Vendor;
use App\Models\Customer;

class PeopleController extends baseController {
    protected $beforeDestroy = 'transactions';

    function getValidationRules($normalText) {
        $phoneRegex = '/^0[567]\d{8}$/';

        return [
            'name' => "required|$normalText",
            'address' => 'emptyOrValid:/\w+/',
            'phone1' => "required|regex:$phoneRegex",
            'phone2' => "emptyOrValid:$phoneRegex",
            'fax' => 'regex:/^0\d{8}$/',
            'notes' => $normalText
        ];
    }
}

class CustomersController extends PeopleController { protected $theClass = Customer::class; }
class VendorsController extends PeopleController { protected $theClass = Vendor::class; }