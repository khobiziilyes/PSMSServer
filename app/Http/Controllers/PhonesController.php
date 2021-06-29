<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\baseController;

use App\Models\Phone;

class PhonesController extends baseController {
    protected $theClass = Phone::class;
    protected $whiteListOrderBy = ['name', 'brand'];
    
    public function indexQuery($request) {
        return Phone::with('Accessories:accessory_id,name,brand,type_id');
    }
}