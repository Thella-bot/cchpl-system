<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerifyEmailController extends Controller
{

use VerifiesEmails;

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
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}