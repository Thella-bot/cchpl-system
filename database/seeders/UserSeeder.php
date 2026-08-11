<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {

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

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->sync([$superAdminRole->id]);
        }

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
