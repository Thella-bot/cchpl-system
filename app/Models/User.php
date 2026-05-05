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

public function hasRole($roleName) {
        return $this->roles()->where('name', $roleName)->exists();
    }

public function hasAnyRole($roleNames) {
        return $this->roles()->whereIn('name', (array)$roleNames)->exists();
    }

public function isSuperAdmin() {
        return $this->hasRole('super_admin');
    }

public function isAdmin() {
        return $this->is_admin;
    }

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

return route('member.dashboard');
    }

public function assignRole($roleName) {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
        return $this;
    }

public function removeRole($roleName) {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }
}