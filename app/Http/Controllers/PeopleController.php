<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ControllersTraits;

use App\Rules\PhoneNumber;
use App\Http\Controllers\baseController;
use App\Models\Vendor;
use App\Models\Customer;

class PeopleController extends baseController {
    use ControllersTraits\storeModel;
    use ControllersTraits\updateModel;
    use ControllersTraits\destroyModel;
    
    protected $beforeDestroy = 'transactions';

    function getValidationRules($resource_id) {
        return [
            'name' => 'required|name',
            
            'phone1' => ['required', new PhoneNumber],
            'phone2' => ['present' , 'nullable', new PhoneNumber],

            'address' => 'present|notes',
            'fax' => 'present|nullable|regex:/^0\d{8}$/',
            'notes' => 'present|notes'
        ];
    }

    function allowedFilters() {
        return ['name'];
    }
}

class CustomersController extends PeopleController { protected $theClass = Customer::class; }
class VendorsController extends PeopleController { protected $theClass = Vendor::class; }