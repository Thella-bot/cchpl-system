<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipCategory;

class MembershipCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Professional',
                'annual_fee' => 400,
                'voting_rights' => true,
                'eligibility_criteria' => '3+ years verifiable experience + good standing',
                'other_notes' => 'Full access to all benefits',
            ],
            [
                'name' => 'Associate',
                'annual_fee' => 250,
                'voting_rights' => false,
                'eligibility_criteria' => '< 3 years or entry-level',
                'other_notes' => 'Limited to events/training',
            ],
            [
                'name' => 'Student/Trainee',
                'annual_fee' => 100,
                'voting_rights' => false,
                'eligibility_criteria' => 'Currently enrolled in accredited program',
                'other_notes' => 'Mentorship priority',
            ],
            [
                'name' => 'Corporate/Institutional',
                'annual_fee' => 2000,
                'voting_rights' => true,
                'eligibility_criteria' => 'Schools, restaurants, hotels, etc.',
                'other_notes' => '1 vote per entity',
            ],
            [
                'name' => 'Honorary',
                'annual_fee' => 0,
                'voting_rights' => false,
                'eligibility_criteria' => 'Nominated by Executive Committee',
                'other_notes' => 'Lifetime, advisory only',
            ],
        ];

        foreach ($categories as $category) {
            MembershipCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
