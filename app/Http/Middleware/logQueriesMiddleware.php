<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class logQueriesMiddleware{
    
    public function handle(Request $request, Closure $next)
    {
        DB::listen(function($query) {
            Storage::disk('local')->append('DB.txt', json_encode(['query' => $query->sql, 'bindings' => $query->bindings],  JSON_PRETTY_PRINT) . "\r\n");
        });

        return $next($request);
    }
}
