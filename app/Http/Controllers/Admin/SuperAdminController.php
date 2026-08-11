<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Internship;
use App\Models\JobListing;
use App\Models\Membership;
use App\Models\MeetingMinute;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'pending_applications' => Membership::where('status', 'pending')->count(),
            'approved_members' => Membership::where('status', 'approved')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'verified_payments' => Payment::where('status', 'verified')->count(),
            'total_revenue' => Payment::where('status', 'verified')->sum('amount'),
            'total_meeting_minutes' => MeetingMinute::count(),
            'total_events' => Event::count(),
            'total_jobs' => JobListing::count(),
            'total_scholarships' => Scholarship::count(),
            'total_internships' => Internship::count(),
            'published_meeting_minutes' => MeetingMinute::where('is_published', true)->count(),
            'published_events' => Event::where('is_published', true)->count(),
            'published_jobs' => JobListing::where('is_published', true)->count(),
            'published_scholarships' => Scholarship::where('is_published', true)->count(),
            'published_internships' => Internship::where('is_published', true)->count(),
        ];

        $recentApplications = Membership::with('user', 'category')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $recentPayments = Payment::with('membership.user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $adminUsers = User::where('is_admin', true)
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.super-admin.dashboard', compact('stats', 'recentApplications', 'recentPayments', 'adminUsers'));
    }

    public function listAdmins()
    {
        $admins = User::where('is_admin', true)
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::all();

        return view('admin.super-admin.admins', compact('admins', 'roles'));
    }

    public function showAdmin(User $user)
    {
        if (! $user->is_admin) {
            return back()->with('error', 'User is not an admin');
        }

        $user->load('roles');
        $roles = Role::all();

        return view('admin.super-admin.show-admin', compact('user', 'roles'));
    }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = AdminService::createAdmin([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'roles' => $request->roles,
        ]);

        if (! $user) {
            return back()->with('error', 'Failed to create admin account. Please check the logs.');
        }

        return back()->with('success', "✅ Admin account created for {$user->name}");
    }

    public function updateAdminRoles(Request $request, User $user)
    {
        if (! $user->is_admin) {
            return back()->with('error', 'User is not an admin');
        }

        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($request->roles);

        return back()->with('success', "✅ Roles updated for {$user->name}");
    }

    public function deactivateAdmin(Request $request, User $user)
    {
        if (! $user->is_admin) {
            return back()->with('error', 'User is not an admin');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', '⚠️ Cannot deactivate your own account');
        }

        $superAdminCount = User::where('is_admin', true)
            ->whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))
            ->count();

        if ($user->hasRole('super_admin') && $superAdminCount <= 1) {
            return back()->with('error', '⚠️ Cannot deactivate the only Super Admin account');
        }

        $user->update(['is_admin' => false]);
        $user->roles()->detach();

        return back()->with('success', "✅ Admin account deactivated for {$user->name}");
    }

    public function auditLog(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('user'), fn ($q) => $q->whereHas('user', fn ($q2) => $q2->where('email', 'like', '%'.$request->user.'%')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', 'like', '%'.$request->action.'%'))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.super-admin.audit-log', compact('logs'));
    }

    public function manageRoles()
    {
        $roles = Role::with('users')->get();

        return view('admin.super-admin.manage-roles', compact('roles'));
    }
}
