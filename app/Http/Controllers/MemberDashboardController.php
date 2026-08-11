<?php

namespace App\Http\Controllers;

use App\Models\Membership;

class MemberDashboardController extends Controller
{
    public function index()
    {

        $memberships = Membership::where('user_id', auth()->id())
            ->with(['category', 'payments' => fn ($q) => $q->orderBy('created_at', 'desc'), 'documents'])
            ->latest()
            ->get();

        $membership = $memberships->first();

        return view('member.dashboard', compact('memberships', 'membership'));
    }
}
