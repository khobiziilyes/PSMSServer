<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Http\Request;

trait updateModel {
	use storeOrUpdateModel;

	public function update(Request $request, $id) {
        return $this->storeOrUpdate($request->input(), $id);
    }
}