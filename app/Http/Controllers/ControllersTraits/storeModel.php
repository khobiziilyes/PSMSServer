<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

trait storeModel {
	use storeOrUpdateModel;

	public function store(Request $request) {
		//Gate::authorize('can', ['C', $this->modelName]);
        $resource = $this->storeOrUpdate($request->input());
        $totalRows = $this->theClass::count();
        
        return [
            'data' => $resource,
            'totalRows' => $totalRows
        ];
    }
}