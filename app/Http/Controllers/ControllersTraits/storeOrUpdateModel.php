<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Support\Facades\Validator;

trait storeOrUpdateModel {
	public function storeOrUpdate($theDatas, $id = null) {
        $isCreate = is_null($id);
        $valArr = $this->getValidationRules($id);
        
        $validatedData = Validator::make($theDatas, $valArr)->validate();
        
        if (method_exists($this, 'formatInput')) $validatedData = $this->formatInput($validatedData, $isCreate);

        $theInstance = null;
        $theClass = $this->theClass;
        
        if ($isCreate) {
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