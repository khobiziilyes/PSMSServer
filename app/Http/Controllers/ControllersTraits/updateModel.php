<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

trait updateModel {
	use storeOrUpdateModel;

	public function update(Request $request, $id) {
		//Gate::authorize('can', ['U', $this->modelName]);
        return $this->storeOrUpdate($request->input(), $id);
    }
}