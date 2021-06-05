<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Http\Request;

trait updateModel {
	use storeOrUpdateModel;

	public function update(Request $request, $id) {
		$this->authorizeAction('Update');
		return $this->storeOrUpdate($request->input(), $id);
    }
}