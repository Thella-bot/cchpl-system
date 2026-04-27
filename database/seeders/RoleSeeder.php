<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder {
    public function run() {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full access to all system functions (root user)'
            ],
            [
                'name' => 'membership_admin',
                'display_name' => 'Membership Administrator',
                'description' => 'Can review and manage membership applications'
            ],
            [
                'name' => 'payment_admin',
                'display_name' => 'Payment Administrator',
                'description' => 'Can verify and process membership payments'
            ],
            [
                'name' => 'reports_admin',
                'display_name' => 'Reports Administrator',
                'description' => 'Can view reports and export member data'
            ],
            [
                'name' => 'finance_admin',
                'display_name' => 'Finance Administrator',
                'description' => 'Can update membership fees and financial settings'
            ],
            [
                'name' => 'content_admin',
                'display_name' => 'Content Administrator',
                'description' => 'Can manage categories and system content'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}