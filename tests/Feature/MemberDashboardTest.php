<?php

namespace Tests\Feature;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_away_from_the_dashboard(): void
    {
        $this->get(route('member.dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_loads_for_a_member_without_a_membership(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('Begin Your Membership Journey');
    }

    public function test_dashboard_shows_membership_status_for_an_approved_member(): void
    {
        $user = User::factory()->create();
        $membership = Membership::factory()->approved()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee($membership->category->name)
            ->assertSee(ucfirst($membership->status))
            ->assertSee('Payment History');
    }

    public function test_dashboard_shows_a_rejected_application_message(): void
    {
        $user = User::factory()->create();
        Membership::factory()->rejected()->create([
            'user_id' => $user->id,
            'rejection_reason' => 'Incomplete documentation.',
        ]);

        $this->actingAs($user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('not approved')
            ->assertSee('Incomplete documentation.');
    }

    public function test_dashboard_displays_payment_history(): void
    {
        $user = User::factory()->create();
        $membership = Membership::factory()->approved()->create(['user_id' => $user->id]);
        $membership->payments()->create([
            'amount' => 350.00,
            'provider' => 'mpesa',
            'purpose' => 'Annual Membership Fee',
            'transaction_reference' => 'CCHPL-20260101-0001',
            'status' => 'verified',
            'verified_at' => now(),
            'receipt_number' => 'RCPT-2026-0001',
        ]);

        $this->actingAs($user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee('350.00')
            ->assertSee('Verified');
    }

    public function test_dashboard_quick_action_links_are_present(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('member.dashboard'))
            ->assertOk()
            ->assertSee(route('member.profile'))
            ->assertSee(route('logout'));
    }
}
