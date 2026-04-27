<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware {
    /**
     * Check if user is super admin
     */
    public function handle(Request $request, Closure $next) {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Super Admin access required');
        }

        return $next($request);
    }
}
