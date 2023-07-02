<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class Checkroles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {if(auth()->check()) {
        $user = User::find(Auth::user()->id);
        if ($user->roles()->whereIn('role_name', ['client', 'owner'])->get()->count()>0)
            return $next($request);
        else
            return response()->json('Authenticated but not Authorized');
    }
    else {
        return response()->json('Unauthenticated');
    }
    }
}
