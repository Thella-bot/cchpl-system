<?php
namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    public function index()
    {
        // Load the user's most recent membership with all related data
        $membership = Membership::where('user_id', auth()->id())
            ->with(['category', 'payments' => fn ($q) => $q->orderBy('created_at', 'desc'), 'documents'])
            ->latest()
            ->first();

        return view('member.dashboard', compact('membership'));
    }
}
