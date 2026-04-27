<?php
namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    public function index()
    {
        // Load all of the user's memberships with related data
        $memberships = Membership::where('user_id', auth()->id())
            ->with(['category', 'payments' => fn ($q) => $q->orderBy('created_at', 'desc'), 'documents'])
            ->latest()
            ->get();

        // Primary membership is the most recent one (for backward-compatible view usage)
        $membership = $memberships->first();

        return view('member.dashboard', compact('memberships', 'membership'));
    }
}
