<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@cchpl.org.ls'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->forceFill([
            'is_admin' => true,
            'email_verified_at' => $superAdmin->email_verified_at ?: now(),
        ])->save();

        // Assign super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->sync([$superAdminRole->id]);
        }

        // Create additional admin users if needed
        $membershipAdmin = User::firstOrCreate(
            ['email' => 'membership@cchpl.org.ls'],
            [
                'name' => 'Membership Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $membershipAdmin->forceFill([
            'is_admin' => true,
            'email_verified_at' => $membershipAdmin->email_verified_at ?: now(),
        ])->save();

        $membershipAdminRole = Role::where('name', 'membership_admin')->first();
        if ($membershipAdminRole) {
            $membershipAdmin->roles()->sync([$membershipAdminRole->id]);
        }
    }
}
