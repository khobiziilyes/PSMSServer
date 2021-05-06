<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\AppendablePaginator;

class baseController extends Controller {
    public function indexQuery() {
        return $this->theClass::query();
    }

    public function index() {
        $query = $this->indexQuery()->with(['created_by_obj:id,name', 'updated_by_obj:id,name'])->filter();
        $paginator = $query->paginateAuto();

        $appendablePaginator = new AppendablePaginator($paginator);
        $appendablePaginator = $appendablePaginator->modelAppend(array_merge(
            ['created_by', 'updated_by'],
            $this->theClass::$indexAppends ?? []
        ));

        return $appendablePaginator;
    }

    public function show($id) {
        return $this->theClass::findOrFail($id);
    }

    public function update(Request $request, $id) {
        return $this->createOrUpdate($request->input(), $id);
    }
    
    public function store(Request $request) {
        return $this->createOrUpdate($request->input());
    }

    public function createOrUpdate($theDatas, $id = null) {
        $normalText = config('app.normalText');
        $valArr = $this->getValidationRules($normalText);

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
        return $theInstance;
    }

    public function destroy($id) {
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