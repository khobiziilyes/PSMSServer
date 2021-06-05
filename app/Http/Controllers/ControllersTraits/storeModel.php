<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Http\Request;

trait storeModel {
	use storeOrUpdateModel;

	public function store(Request $request) {
		$this->authorizeAction('Write');

        $resource = $this->storeOrUpdate($request->input());
        $totalRows = $this->theClass::count();
        
        return [
            'data' => $resource,
            'totalRows' => $totalRows
        ];
    }
}