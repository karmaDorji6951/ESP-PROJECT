<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = optional(Auth::user()->role)->slug ?? optional(Auth::user()->role)->name;

        if (! in_array($userRole, $roles, true) && ! in_array(optional(Auth::user()->role)->name, $roles, true)) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}