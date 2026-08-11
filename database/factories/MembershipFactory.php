<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => MembershipCategory::factory(),
            'status' => Membership::STATUS_PENDING,
            'member_id' => null,
            'expiry_date' => null,
            'suspended_at' => null,
            'rejection_reason' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Membership::STATUS_APPROVED,
            'expiry_date' => now()->addYear(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Membership::STATUS_PENDING,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Membership::STATUS_REJECTED,
            'rejection_reason' => 'Does not meet requirements.',
        ]);
    }
}
