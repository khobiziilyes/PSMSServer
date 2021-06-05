<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isAdminMiddleware{
    public function handle(Request $request, Closure $next) {
        if ($request->user()->isAdmin) return $next($request);
    }
}
