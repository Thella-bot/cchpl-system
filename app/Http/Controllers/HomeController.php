<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasAnyRole(['super_admin', 'membership_admin', 'finance_admin', 'reports_admin'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('member.dashboard');
        }

        return view('welcome', [
            'categories' => \App\Models\MembershipCategory::query()
                ->where('name', '!=', 'Honorary')
                ->orderBy('annual_fee')
                ->get(),
        ]);
    }
}
