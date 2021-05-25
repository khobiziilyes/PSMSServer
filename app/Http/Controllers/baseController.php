<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Http\Resources\AppendablePaginator;

class baseController extends Controller {
    public function indexQuery() {
        return $this->theClass::query();
    }

    public function index(Request $request) {
        //Gate::authorize('can', ['R', $this->modelName]);

        $query = $this->indexQuery();
        if (($this->withTrashed ?? false) && $request->query->has('withTrashed')) $query->withTrashed();
        
        return $this->paginateQuery($query);
    }

    public function paginateQuery($query) {
        $query = $query->with(['created_by_obj:id,name', 'updated_by_obj:id,name']);
        $query = $query->filter(request()->all());

        $orderBy = request()->query('orderBy', null);
        
        if (filled($orderBy) && is_string($orderBy) && in_array($orderBy, array_merge($this->whiteListOrderBy ?? [], ['id', 'created_at']))) {
            $direction = request()->query('dir', 'asc');
            if (!(filled($direction) && is_string($direction) && $direction === 'desc')) $direction = 'asc';
            
            $query = $query->orderBy($orderBy, $direction);
        }

        $paginator = $query->paginateAuto();

        $appendablePaginator = new AppendablePaginator($paginator);
        $appendablePaginator = $appendablePaginator->modelAppend(array_merge(
            ['created_by', 'updated_by'],
            $this->theClass::$indexAppends ?? []
        ));

        return $appendablePaginator;
    }
    
    public function show($id) {
        //Gate::authorize('can', ['R', $this->modelName]);
        return $this->theClass::findOrFail($id);
    }
}