<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AppendablePaginator;

class baseController extends Controller {
    public function index(Request $request) {
        $query = $this->theClass::query();
        if (($this->withTrashed ?? false) && $request->query->has('withTrashed')) $query->withTrashed();

        $query = $query->with(['created_by_obj:id,name', 'updated_by_obj:id,name'])->filter();
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
}