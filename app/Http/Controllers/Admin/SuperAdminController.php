<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SuperAdminController extends Controller {
    /**
     * Super admin dashboard with system overview
     */
    public function dashboard() {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('is_admin', true)->count(),
            'pending_applications' => Membership::where('status', 'pending')->count(),
            'approved_members' => Membership::where('status', 'approved')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'verified_payments' => Payment::where('status', 'verified')->count(),
            'total_revenue' => Payment::where('status', 'verified')->sum('amount'),
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

    /**
     * List all admin users with their roles
     */
    public function listAdmins() {
        $admins = User::where('is_admin', true)
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::all();

        return view('admin.super-admin.admins', compact('admins', 'roles'));
    }

    /**
     * View admin user details
     */
    public function showAdmin(User $user) {
        if (!$user->is_admin) {
            return back()->with('error', 'User is not an admin');
        }

        $user->load('roles');
        $roles = Role::all();

        return view('admin.super-admin.show-admin', compact('user', 'roles'));
    }

    /**
     * Create new admin account
     */
    public function createAdmin(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'is_admin' => true
        ]);

        // Assign roles
        $user->roles()->sync($request->roles);

        return back()->with('success', "✅ Admin account created for {$user->name}");
    }

    /**
     * Update admin roles
     */
    public function updateAdminRoles(Request $request, User $user) {
        if (!$user->is_admin) {
            return back()->with('error', 'User is not an admin');
        }

        $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($request->roles);

        return back()->with('success', "✅ Roles updated for {$user->name}");
    }

    /**
     * Deactivate admin account
     */
    public function deactivateAdmin(Request $request, User $user) {
        if (!$user->is_admin) {
            return back()->with('error', 'User is not an admin');
        }

        // Prevent deactivating self or only super admin
        if ($user->id === auth()->id()) {
            return back()->with('error', '⚠️ Cannot deactivate your own account');
        }

        $superAdminCount = User::where('is_admin', true)
            ->whereHas('roles', fn($q) => $q->where('name', 'super_admin'))
            ->count();

        if ($user->hasRole('super_admin') && $superAdminCount <= 1) {
            return back()->with('error', '⚠️ Cannot deactivate the only Super Admin account');
        }

        $user->update(['is_admin' => false]);
        $user->roles()->detach();

        return back()->with('success', "✅ Admin account deactivated for {$user->name}");
    }

    /**
     * System audit log (view recent actions)
     */
    public function auditLog(Request $request) {
        $logs = AuditLog::with('user')
            ->when($request->filled('user'), fn($q) => $q->whereHas('user', fn($q2) => $q2->where('email', 'like', '%'.$request->user.'%')))
            ->when($request->filled('action'), fn($q) => $q->where('action', 'like', '%'.$request->action.'%'))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.super-admin.audit-log', compact('logs'));
    }

    /**
     * Manage system roles
     */
    public function manageRoles() {
        $roles = Role::with('users')->get();
        
        return view('admin.super-admin.manage-roles', compact('roles'));
    }
}
