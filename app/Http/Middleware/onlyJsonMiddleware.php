<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class onlyJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if (!(
            in_array($request->headers->get('accept'), ['application/json', 'Application/Json']) &&
            in_array($request->headers->get('content-type'), ['application/json', 'Application/Json'])
        )) return response()->json(['message' => 'Not accepted.'], 406);

        return $next($request);
    }
}
