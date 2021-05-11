<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Http\Request;

trait storeModel {
	use storeOrUpdateModel;

	public function store(Request $request) {
        return $this->storeOrUpdate($request->input());
    }
}