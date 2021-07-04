<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Support\Facades\Validator;

trait storeOrUpdateModel {
	public function storeOrUpdate($theDatas, $id = null) {
        $valArr = $this->getValidationRules(!is_null($id));
        
        $validatedData = Validator::make($theDatas, $valArr)->validate();
        
        $theInstance = null;
        $theClass = $this->theClass;
        
        if (is_null($id)) {
            $theInstance = new $theClass($validatedData);
        } else {
            $theInstance = $theClass::findOrFail($id);
            $theInstance->fill($validatedData);
        }
        
        $theInstance->save();
        
        $totalRows = $theClass::count();
        
        return $this->instanceResponse(request(), $theInstance);
    }
}