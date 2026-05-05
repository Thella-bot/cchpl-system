<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{

use AuthenticatesUsers;

public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

protected function redirectTo(): string
    {
        $user = auth()->user();

if ($user && $user->isAdmin()) {
            return $user->adminHome();
        }

return '/member/dashboard';
    }

protected function authenticated($request, $user)
    {
        $user->update(['last_login_at' => now()]);
    }
}
