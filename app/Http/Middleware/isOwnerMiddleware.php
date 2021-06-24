<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isOwnerMiddleware {
    public function handle(Request $request, Closure $next) {
        if ($request->user()->append('isOwner')->isOwner) return $next($request);
        return abort(404);
    }
}
