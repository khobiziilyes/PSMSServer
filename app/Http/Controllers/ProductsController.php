<?php

namespace App\Http\Controllers;

use Bouncer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\ControllersTraits;
use App\Http\Controllers\baseController;

use App\Models\Phone;
use App\Models\Accessory;

class ProductsController extends baseController {
    use ControllersTraits\storeModel;
    use ControllersTraits\updateModel;
    use ControllersTraits\destroyModel;

    protected $beforeDestroy = 'items';
    static $isPublicProperty = true;

    public function _allowedFilters($more = []) {
        return array_merge(['name', 'brand'], $more);
    }

    public function _getValidationRules($resource_id, $more = []) {
    	$rules = [
    		'name' => 'required|name',
    		'notes' => 'present|notes'
    	];

        if (is_null($resource_id)) $rules['is_public'] = 'required|boolean';

        return array_merge($rules, $more);
    }

    public function formatInput($validatedData, $isCreate) {
        // if ($validatedData['is_public']) Bouncer::authorize('addToPublic');
        return $validatedData;
    }
}