<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! auth()->check()) {
            return redirect('login');
        }

        if (auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        if (! auth()->user()->hasAnyRole($roles)) {
            abort(403, 'Insufficient permissions for this action');
        }

        return $next($request);
    }
}
