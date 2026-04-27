<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RoleManagementException;

/**
 * Service class for managing administrator users and their roles.
 * This centralizes the logic for admin creation, role assignment, and access control.
 */
class AdminService
{
    /**
     * Create a new standard administrator user.
     *
     * @param array $data Expects ['name', 'email', 'password', 'phone'?, 'roles'?]
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

            if ($user && !empty($data['roles'])) {
                $validRoles = Role::whereIn('id', $data['roles'])->pluck('id');
                $user->roles()->sync($validRoles);
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to create admin user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create the first Super Admin user.
     * This should typically only be run once from tinker during setup.
     *
     * @param array $data Expects ['name', 'email', 'password', 'phone'?]
     * @return User|null
     */
    public static function createSuperAdmin(array $data): ?User
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            Log::critical("Super Admin role not found. Ensure 'php artisan db:seed --class=RoleSeeder' has been run.");
            return null;
        }

        $data['roles'] = [$superAdminRole->id];

        return self::createAdmin($data);
    }

    /**
     * Get all users with the admin flag, with their roles eager-loaded.
     *
     * @return Collection
     */
    public static function getAllAdmins(): Collection
    {
        return User::where('is_admin', true)->with('roles')->orderBy('name')->get();
    }

    /**
     * Get all admin users who have a specific role.
     *
     * @param string $roleName e.g., 'membership_admin'
     * @return Collection
     */
    public static function getAdminsByRole(string $roleName): Collection
    {
        return User::whereHas('roles', fn($query) => $query->where('name', $roleName))->get();
    }

    /**
     * Update the roles for a given user with dependency checks.
     *
     * @param User $user The user to update.
     * @param array $roleIds The array of role IDs to sync.
     * @throws RoleManagementException
     */
    public static function updateUserRoles(User $user, array $roleIds): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        // Prevent removing the super_admin role from the last super admin
        if (
            $superAdminRole &&
            $user->roles->contains($superAdminRole) &&
            !in_array($superAdminRole->id, $roleIds) &&
            self::isLastSuperAdmin($user)
        ) {
            throw new RoleManagementException('Cannot remove the Super Admin role from the last Super Admin.');
        }

        $validRoles = Role::whereIn('id', $roleIds)->pluck('id');
        $user->roles()->sync($validRoles);
    }

    /**
     * Revoke all admin privileges from a user.
     * Sets is_admin to false and removes all associated roles.
     *
     * @throws RoleManagementException
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

        if (!$superAdminRole || !$user->roles->contains($superAdminRole)) {
            return false; // Not a super admin, so not the last one.
        }

        $superAdminCount = DB::table('user_roles')->where('role_id', $superAdminRole->id)->count();

        return $superAdminCount <= 1;
    }
}