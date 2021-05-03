<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class baseController extends Controller {
    public function index() {
        return $this->theClass::filter()->paginateAuto();
    }
    
    public function store(Request $request) {
        return $this->createOrUpdate($request->input());
    }

    public function show($id) {
        return $this->theClass::findOrFail($id);
    }

    public function update(Request $request, $id) {
        return $this->createOrUpdate($request->input(), $id);
    }

    public function createOrUpdate($theDatas, $id = null, $save = true) {
        $normalText = config('app.normalText');
        $valArr = $this->getValidationRules($normalText);

        $validatedData = Validator::make($theDatas, $valArr)->validate();
        
        $theInstance = null;
        $theClass = $this->theClass;
        
        if (is_null($id)) {
            $theInstance = new $theClass($validatedData);
        } else {
            $theInstance = $theClass::findOrFail($id); // What if it's a trick id?
            $theInstance->fill($validatedData);
        }
        
        if ($save) $theInstance->save();
        return $theInstance;
    }

    function destroy($id) {
        if (property_exists($this, 'beforeDestroy')) {
            $theInstance = $this->theClass::findOrFail($id);
            $beforeDestroy = $this->beforeDestroy;
            
            $itemsCount = is_null($beforeDestroy) ? 0 : $theInstance->$beforeDestroy->count();
            $deleted = false;
            
            if ($itemsCount === 0) {
                $theInstance->delete();
                $deleted = true;
            }
            
            return ['deleted' => $deleted];
        }
        
        return abort(404);
    }
}