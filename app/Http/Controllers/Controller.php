<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function paginatorResponse($paginator) {
        return Arr::only(is_array($paginator) ? $paginator : $paginator->toArray(), [
    		'data',
    		'current_page',
    		'from',
    		'last_page',
    		'per_page',
    		'to',
    		'total'
    	]);
    }

    public function instanceResponse($request, $theInstance) {
        return [
            'data' => $theInstance,
            'totalRows' => $this->indexQuery($request)->count()
        ];
    }
}
