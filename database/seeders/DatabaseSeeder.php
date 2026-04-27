<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Call your individual seeders here
        $this->call([
            RoleSeeder::class,
            MembershipCategorySeeder::class,
            UserSeeder::class,
            ResignationSeeder::class
            // Add other seeders as needed
        ]);
    }
}
