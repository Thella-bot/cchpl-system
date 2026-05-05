<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{

use ResetsPasswords;

protected function redirectTo(): string
    {
        $user = auth()->user();

if ($user && $user->isAdmin()) {
            return $user->adminHome();
        }

return '/member/dashboard';
    }

public function __construct()
    {
        $this->middleware('guest');
    }
}