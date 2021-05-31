<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ControllersTraits;

use App\Http\Controllers\baseController;
use App\Models\Vendor;
use App\Models\Customer;

class PeopleController extends baseController {
    use ControllersTraits\storeModel;
    use ControllersTraits\updateModel;
    use ControllersTraits\destroyModel;
    
    protected $beforeDestroy = 'transactions';

    function getValidationRules($isUpdate) {
        $phoneRegex = '/^0[567]\d{8}$/';        
        $required = $isUpdate ? '' : 'required|';

        return [
            'name' => $required . 'name',
            'phone1' => $required . "regex:$phoneRegex",  

            'address' => 'nullable|notes',
            'phone2' => "nullable|regex:$phoneRegex",
            'fax' => 'nullable|regex:/^0\d{8}$/',
            'notes' => 'notes'
        ];
    }
}

class CustomersController extends PeopleController { protected $theClass = Customer::class; protected $modelName = 'customers'; }
class VendorsController extends PeopleController { protected $theClass = Vendor::class; protected $modelName = 'vendors'; }