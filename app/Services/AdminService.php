<?php

namespace App\Services;

use App\Exceptions\RoleManagementException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminService
{
    /**
     * Create a new admin user with the given data and assign roles.
     *
     * @param  array  $data  User data including name, email, password, and roles.
     * @return User|null The created user or null on failure.
     */
    public static function createAdmin(array $data): ?User
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'is_admin' => true,
            ]);

            if ($user && ! empty($data['roles'])) {
                $validRoles = Role::whereIn('id', $data['roles'])->pluck('id');
                $user->roles()->sync($validRoles);
            }

            return $user;
        } catch (\Throwable $e) {
            Log::error('Failed to create admin user: '.$e->getMessage(), [
                'email' => $data['email'] ?? null,
            ]);

            return null;
        }
    }

    /**
     * Create a new super admin user.
     *
     * Ensures the super_admin role exists and assigns it to the new user.
     *
     * @param  array  $data  User data including name, email, and password.
     * @return User|null The created super admin or null on failure.
     */
    public static function createSuperAdmin(array $data): ?User
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (! $superAdminRole) {
            Log::critical("Super Admin role not found. Ensure 'php artisan db:seed --class=RoleSeeder' has been run.");

            return null;
        }

        $data['roles'] = [$superAdminRole->id];

        return self::createAdmin($data);
    }

    /**
     * Retrieve all admin users with their roles.
     *
     * @return Collection List of admin users.
     */
    public static function getAllAdmins(): Collection
    {
        return User::where('is_admin', true)->with('roles')->orderBy('name')->get();
    }

    /**
     * Retrieve all admin users by a specific role name.
     *
     * @param  string  $roleName  The name of the role to filter admins by.
     * @return Collection List of admin users with the specified role.
     */
    public static function getAdminsByRole(string $roleName): Collection
    {
        return User::where('is_admin', true)
            ->whereHas('roles', fn ($query) => $query->where('name', $roleName))
            ->with('roles')
            ->orderBy('name')
            ->get();
    }

    /**
     * Update roles for an admin user with guardrails for last super admin.
     */
    public static function updateUserRoles(User $user, array $roleIds): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (
            $superAdminRole &&
            $user->roles->contains($superAdminRole) &&
            ! in_array($superAdminRole->id, $roleIds, true) &&
            self::isLastSuperAdmin($user)
        ) {
            throw new RoleManagementException('Cannot remove the Super Admin role from the last Super Admin.');
        }

        $validRoles = Role::whereIn('id', $roleIds)->pluck('id');
        $user->roles()->sync($validRoles);
    }

    /**
     * Revoke admin access (disable is_admin and clear role assignments) with guardrails.
     */
    public static function revokeAdminAccess(User $user): void
    {
        if (self::isLastSuperAdmin($user)) {
            throw new RoleManagementException('Cannot deactivate the last Super Admin.');
        }

        $user->roles()->sync([]);
        $user->update(['is_admin' => false]);
    }

    private static function isLastSuperAdmin(User $user): bool
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (! $superAdminRole || ! $user->roles->contains($superAdminRole)) {
            return false;
        }

        $superAdminCount = DB::table('user_roles')
            ->where('role_id', $superAdminRole->id)
            ->count();

        return $superAdminCount <= 1;
    }
}
