<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the post-login redirect path for the user.
     */
    protected function redirectTo(): string
    {
        $user = auth()->user();

if ($user && $user->isAdmin()) {
            return '/admin/dashboard';
        }

        return '/member/dashboard';
    }

    /**
     * The user has been authenticated.
     * Update last_login_at timestamp for audit and reporting.
     */
    protected function authenticated($request, $user)
    {
        $user->update(['last_login_at' => now()]);
    }
}
