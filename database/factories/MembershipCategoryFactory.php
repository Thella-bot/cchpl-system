<?php

namespace Database\Factories;

use App\Models\MembershipCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipCategoryFactory extends Factory
{
    protected $model = MembershipCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Professional Member',
                'Associate Member',
                'Student Member',
                'Corporate Member',
                'Individual Member',
            ]),
            'annual_fee' => fake()->randomFloat(2, 100, 1000),
            'joining_fee' => fake()->randomFloat(2, 0, 200),
            'voting_rights' => fake()->boolean(),
            'eligibility_criteria' => fake()->sentence(),
            'other_notes' => fake()->sentence(),
        ];
    }
}
