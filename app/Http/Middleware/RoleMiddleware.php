<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware {
    /**
     * Check if user has required role
     */
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!auth()->check()) {
            return redirect('login');
        }

        // Super admin always has access
        if (auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!auth()->user()->hasAnyRole($roles)) {
            abort(403, 'Insufficient permissions for this action');
        }

        return $next($request);
    }
}
