<?php

namespace Tests\Unit\Services;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\User;
use App\Services\MembershipService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MembershipServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 1, 15));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_generates_correct_member_id_format()
    {
        $user = User::factory()->create();
        $category = MembershipCategory::factory()->create(['name' => 'Individual Member']);
        $membership = Membership::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $memberId = (new MembershipService)->generateMemberId($membership);

        $this->assertMatchesRegularExpression('/^CCHPL-MEM-2026-\\d{3}$/', $memberId);
    }

    public function test_it_correctly_identifies_penalty_applicable()
    {
        // Expired in the previous year (2025): past the 31 March due date -> penalty.
        $membership = Membership::factory()->create([
            'status' => 'approved',
            'expiry_date' => Carbon::create(2025, 12, 1),
        ]);

        $this->assertTrue((new MembershipService)->isPenaltyApplicable($membership));
    }

    public function test_it_identifies_no_penalty_within_grace_period()
    {
        // Expired earlier this year (2026) but before 31 March -> still in grace.
        $membership = Membership::factory()->create([
            'status' => 'approved',
            'expiry_date' => Carbon::create(2026, 1, 1),
        ]);

        $this->assertFalse((new MembershipService)->isPenaltyApplicable($membership));
    }

    public function test_it_calculates_correct_next_march_expiry_date()
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 15));

        $expiry = PaymentService::nextMarchExpiry();

        $this->assertEquals('2027-03-31', $expiry->format('Y-m-d'));
    }
}
