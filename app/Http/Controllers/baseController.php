<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class baseController extends Controller {
    public function indexQuery($request) {
        return $this->theClass::query();
    }

    public function index(Request $request) {
        $this->authorizeAction('Read');
        
        $query = $this->indexQuery($request);
        return $this->paginateQuery($query, $request);
    }

    public function paginateQuery($query, $request) {
        $query->with(['created_by_obj:id,name', 'updated_by_obj:id,name']);
        
        $filterFields = array_merge(method_exists($this, 'allowedFilters') ? $this->allowedFilters() : [], [
            'search',
            
            'createdBy',
            'createdBefore',
            'createdAfter',

            'updatedBy',
            'updatedBefore',
            'updatedAfter'
        ]);
        
        $orderBy = $request->query('orderBy', null);
        
        if (filled($orderBy) && is_string($orderBy) && in_array($orderBy, array_merge($this->whiteListOrderBy ?? [], ['id', 'created_at', 'created_by']))) {
            $direction = $request->query('dir', 'asc');
            if (!(filled($direction) && is_string($direction) && $direction === 'desc')) $direction = 'asc';
            
            $query->orderBy($orderBy, $direction);
        }
        
        $filterableFields = Arr::only($request->query(), $filterFields);
        $query->filter($filterableFields);
        
        $paginator = $query->paginateAuto();

        $paginator->getCollection()->map(function($a) use ($request) {
            $a->append(['created_by', 'updated_by', 'isWritable']);
        });

        if (method_exists($this, 'formatOutput')) $this->formatOutput($paginator->getCollection(), $request);

        return $this->paginatorResponse($paginator);
    }
    
    public function show($id) {
        $this->authorizeAction('Read');
        return $this->theClass::findOrFail($id);
    }

    public function authorizeAction($type, $name = null) {
        return \Bouncer::authorize('can' . $type . ($name ? $name : $this->theClass::getClassName()));
    }
}