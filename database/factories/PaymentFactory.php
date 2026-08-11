<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'membership_id' => Membership::factory(),
            'amount' => fake()->randomFloat(2, 100, 1000),
            'provider' => fake()->randomElement(['mpesa', 'ecocash']),
            'purpose' => 'Annual Membership Fee',
            'transaction_reference' => 'CCHPL-'.now()->format('Ymd').'-'.str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'proof_file' => null,
            'status' => Payment::STATUS_PENDING,
            'verification_notes' => null,
            'verified_at' => null,
            'receipt_number' => null,
            'transaction_id' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => now(),
            'receipt_number' => 'RCPT-'.now()->year.'-'.str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_REJECTED,
        ]);
    }
}
