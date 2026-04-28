<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;
    protected $fillable = ["name", "email", "password", "phone", "organization", "is_admin"];
    protected $hidden = ["password"];
    protected $dates = ['last_login_at'];
    
    public function memberships() {
        return $this->hasMany(Membership::class);
    }
    
    public function roles() {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
    
    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName) {
        return $this->roles()->where('name', $roleName)->exists();
    }
    
    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roleNames) {
        return $this->roles()->whereIn('name', (array)$roleNames)->exists();
    }
    
    /**
     * Check if user is super admin (root user)
     */
    public function isSuperAdmin() {
        return $this->hasRole('super_admin');
    }
    
    /**
     * Check if user is any type of admin
     */
    public function isAdmin() {
        return $this->is_admin;
    }

    /**
     * Get the appropriate admin home route based on user roles.
     * Prevents non-super admins from being redirected to the super-admin-only dashboard.
     */
    public function adminHome() {
        if ($this->isSuperAdmin()) {
            return route('admin.dashboard');
        }

        if ($this->hasRole('membership_admin')) {
            return route('admin.memberships.index');
        }

        if ($this->hasRole('payment_admin')) {
            return route('admin.payments.index');
        }

        if ($this->hasRole('finance_admin')) {
            return route('admin.memberships.categories.index');
        }

        if ($this->hasRole('reports_admin')) {
            return route('admin.reports.index');
        }

        // Fallback for any admin without specific roles
        return route('member.dashboard');
    }
    
    /**
     * Assign role to user
     */
    public function assignRole($roleName) {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
        return $this;
    }
    
    /**
     * Remove role from user
     */
    public function removeRole($roleName) {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }
}