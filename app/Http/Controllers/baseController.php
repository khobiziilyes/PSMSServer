<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class baseController extends Controller {
    public function indexQuery($request) {
        return $this->theClass::query();
    }

    public function index(Request $request) {
        //Gate::authorize('can', ['R', $this->modelName]);

        $query = $this->indexQuery($request);
        if (($this->withTrashed ?? false) && $request->query->has('withTrashed')) $query->withTrashed();
        
        return $this->paginateQuery($query, $request);
    }

    public function paginateQuery($query, $request) {
        $query = $query->with(['created_by_obj:id,name', 'updated_by_obj:id,name']);
        $filterFields = array_merge(method_exists($this, 'allowedFilters') ? $this->allowedFilters() : [], [
            'createdBy',
            'createdBefore',
            'createdAfter',

            'updatedBy',
            'updatedBefore',
            'updatedAfter'
        ]);
        
        $query = $query->filter(Arr::only($request->query(), $filterFields));

        $orderBy = $request->query('orderBy', null);
        
        if (filled($orderBy) && is_string($orderBy) && in_array($orderBy, array_merge($this->whiteListOrderBy ?? [], ['id', 'created_at']))) {
            $direction = $request->query('dir', 'asc');
            if (!(filled($direction) && is_string($direction) && $direction === 'desc')) $direction = 'asc';
            
            $query = $query->orderBy($orderBy, $direction);
        }

        $paginator = $query->paginateAuto();

        $paginator->getCollection()->map(function($a) use ($request) {
            $a->append(['created_by', 'updated_by']);
        });

        if (method_exists($this, 'formatData')) $this->formatData($paginator->getCollection(), $request);

        return Arr::only($paginator->toArray(), ['data', 'current_page', 'from', 'last_page', 'per_page', 'to', 'total']);
    }
    
    public function show($id) {
        //Gate::authorize('can', ['R', $this->modelName]);
        return $this->theClass::findOrFail($id);
    }
}