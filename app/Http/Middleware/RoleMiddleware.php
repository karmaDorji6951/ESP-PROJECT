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

        $normalize = static fn ($value): ?string => $value === null
            ? null
            : strtolower(trim((string) $value));

        $allowedRoles = array_values(array_filter(array_map($normalize, $roles)));

        $userRoleSlug = $normalize(optional(Auth::user()->role)->slug);
        $userRoleName = $normalize(optional(Auth::user()->role)->name);

        if (! in_array($userRoleSlug, $allowedRoles, true) && ! in_array($userRoleName, $allowedRoles, true)) {
            abort(403, 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}