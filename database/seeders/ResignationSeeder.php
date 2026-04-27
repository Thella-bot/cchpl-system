<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\Resignation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates sample resignation records for testing the workflow.
     */
    public function run(): void
    {
        // Find some approved members to create resignations for.
        $membersToResign = Membership::where('status', 'approved')
            ->with('user')
            ->inRandomOrder()
            ->take(3)
            ->get();

        if ($membersToResign->isEmpty()) {
            $this->command->warn('No approved members found to create sample resignations. Skipping ResignationSeeder.');
            return;
        }

        // Find an admin user to acknowledge a resignation.
        $admin = User::where('is_admin', true)->inRandomOrder()->first();

        // --- Seeder Record 1: A pending resignation ---
        $member1 = $membersToResign->first();
        if ($member1) {
            Resignation::create([
                'user_id' => $member1->user_id,
                'membership_id' => $member1->id,
                'status' => 'pending',
                'effective_date' => now()->addDays(30),
                'reason_code' => 'personal',
                'reason_notes' => 'Moving to a different country for personal reasons. Thank you for the opportunities.',
                'balance_outstanding' => 0,
            ]);
        }

        // --- Seeder Record 2: An already acknowledged resignation ---
        $member2 = $membersToResign->get(1);
        if ($member2 && $admin) {
            Resignation::create([
                'user_id' => $member2->user_id,
                'membership_id' => $member2->id,
                'status' => 'acknowledged',
                'effective_date' => now()->subDays(5),
                'reason_code' => 'career_change',
                'reason_notes' => 'Leaving the hospitality industry for a new career path.',
                'balance_outstanding' => 150.00,
                'acknowledged_by' => $admin->id,
                'acknowledged_at' => now()->subDays(2),
                'acknowledgement_notes' => 'Resignation acknowledged. We wish you the best in your new career. Please settle the outstanding balance at your earliest convenience.',
            ]);
        }

        // --- Seeder Record 3: Another pending resignation ---
        $member3 = $membersToResign->get(2);
        if ($member3) {
            Resignation::create([
                'user_id' => $member3->user_id,
                'membership_id' => $member3->id,
                'status' => 'pending',
                'effective_date' => now()->addDays(14),
                'reason_code' => 'dissatisfied',
                'reason_notes' => 'I feel the council is not providing enough value for the membership fee.',
                'balance_outstanding' => 0,
            ]);
        }

        $this->command->info('Sample resignations seeded successfully.');
    }
}