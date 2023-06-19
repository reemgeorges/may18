<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {//http_response_code(500);
  //  dd(Auth::user()  );
        if(Auth::user()!=null) {
        if (auth()->user()->name == 'my name')
            return $next($request);
        else {
            return response('Uthenticated but not authorized', 401);
        }
    }
    else
        return response('Unuthenticated ');
    }
}
