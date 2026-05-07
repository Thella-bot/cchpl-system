<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User model representing council members and administrators.
 * 
 * Handles authentication, role-based access control, and membership relationships.
 * 
 * @package App\Models
 */
class User extends Authenticatable implements MustVerifyEmail {
    use Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["name", "email", "password", "phone", "organization", "is_admin"];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ["password", "remember_token"];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the memberships belonging to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Many-to-many relationship between user and roles through user_roles pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has specific role.
     *
     * @param string $roleName
     * @return bool
     */
public function hasRole($roleName) {
        return $this->roles()->where('name', $roleName)->exists();
    }

/**
     * Check if user has any of the specified roles.
     *
     * @param array|string $roleNames
     * @return bool
     */
public function hasAnyRole($roleNames) {
        return $this->roles()->whereIn('name', (array)$roleNames)->exists();
    }

    /**
     * Check if user is super administrator.
     *
     * @return bool
     */
public function isSuperAdmin() {
        return $this->hasRole('super_admin');
    }

/**
     * Check if user has admin flag enabled (legacy boolean flag).
     * 
     * @return bool
     */
public function isAdmin() {
        return $this->is_admin;
    }

    /**
     * Determine the appropriate dashboard route based on user roles.
     * 
     * Role hierarchy: super_admin > specific admin roles > member dashboard
     * 
     * @return string Named route
     */
public function adminHome() {
        // Super admins get full dashboard access
        if ($this->isSuperAdmin()) {
            return route('admin.dashboard');
        }

        // Role-specific admin dashboards
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

        if ($this->hasRole('content_admin')) {
            return route('admin.content.dashboard');
        }

return route('member.dashboard');
    }

    /**
     * Assign role to user (idempotent - won't duplicate).
     * Chainable method.
     *
     * @param string $roleName
     * @return $this
     */
    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
        return $this;
    }

/**
     * Remove a role from the user.
     * 
     * @param string $roleName
     * @return $this
     */
public function removeRole($roleName) {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }
}
