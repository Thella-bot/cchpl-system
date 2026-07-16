<?php

namespace Tests\Unit\Services;

use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\User;
use App\Services\MembershipService;
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

    public function it_generates_correct_member_id_format()
    {
        $user = User::factory()->create();
        $category = MembershipCategory::factory()->create(['code' => 'IND']);
        $membership = Membership::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $memberId = (new MembershipService())->generateMemberId($membership);
        
        $this->assertMatchesRegularExpression('/^CCHPL-IND-2026-\\d{4}$/', $memberId);
    }

    public function it_correctly_identifies_penalty_applicable()
    {
        $membership = Membership::factory()->create([
            'status' => 'approved',
            'expiry_date' => Carbon::now()->subDays(45),
        ]);

        $this->assertTrue((new MembershipService())->isPenaltyApplicable($membership));
    }

    public function it_identifies_no_penalty_within_grace_period()
    {
        $membership = Membership::factory()->create([
            'status' => 'approved',
            'expiry_date' => Carbon::now()->subDays(15),
        ]);

        $this->assertFalse((new MembershipService())->isPenaltyApplicable($membership));
    }

    public function it_calculates_correct_next_expiry_date()
    {
        $service = new MembershipService();
        $expiry = $service->calculateNextExpiry();
        
        $this->assertEquals('2027-03-31', $expiry->format('Y-m-d'));
    }
}