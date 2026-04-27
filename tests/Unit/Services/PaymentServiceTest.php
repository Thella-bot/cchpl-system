<?php

namespace Tests\Unit\Services;

use App\Models\Payment;
use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** @test */
    public function it_generates_sequential_receipt_numbers_for_current_financial_year()
    {
        Carbon::setTestNow(Carbon::create(2025, 5, 15));
        $membership = $this->createMembership();

        Payment::query()->create([
            'membership_id' => $membership->id,
            'amount' => 100,
            'provider' => 'mpesa',
            'purpose' => 'annual_fee',
            'transaction_reference' => 'CCHPL-20250515-0001',
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => Carbon::create(2025, 4, 10),
        ]);

        Payment::query()->create([
            'membership_id' => $membership->id,
            'amount' => 100,
            'provider' => 'mpesa',
            'purpose' => 'annual_fee',
            'transaction_reference' => 'CCHPL-20250515-0002',
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => Carbon::create(2025, 5, 1),
        ]);

        $receipt = PaymentService::generateReceiptNumber();

        $this->assertEquals('RCPT-2025-0003', $receipt);
    }

    /** @test */
    public function it_handles_receipt_numbers_in_january_march_as_part_of_previous_calendar_year_fy()
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 15));
        $membership = $this->createMembership();

        Payment::query()->create([
            'membership_id' => $membership->id,
            'amount' => 100,
            'provider' => 'ecocash',
            'purpose' => 'annual_fee',
            'transaction_reference' => 'CCHPL-20260215-0001',
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => Carbon::create(2025, 4, 1),
        ]);

        Payment::query()->create([
            'membership_id' => $membership->id,
            'amount' => 100,
            'provider' => 'ecocash',
            'purpose' => 'annual_fee',
            'transaction_reference' => 'CCHPL-20260215-0002',
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => Carbon::create(2026, 1, 10),
        ]);

        $receipt = PaymentService::generateReceiptNumber();

        $this->assertEquals('RCPT-2025-0003', $receipt);
    }

    /** @test */
    public function it_resets_sequence_for_new_financial_year()
    {
        $membership = $this->createMembership();

        Payment::query()->create([
            'membership_id' => $membership->id,
            'amount' => 100,
            'provider' => 'mpesa',
            'purpose' => 'annual_fee',
            'transaction_reference' => 'CCHPL-20240601-0001',
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => Carbon::create(2024, 6, 1),
        ]);

        Payment::query()->create([
            'membership_id' => $membership->id,
            'amount' => 100,
            'provider' => 'mpesa',
            'purpose' => 'annual_fee',
            'transaction_reference' => 'CCHPL-20250201-0001',
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => Carbon::create(2025, 2, 1),
        ]);

        Carbon::setTestNow(Carbon::create(2025, 4, 1));

        $receipt = PaymentService::generateReceiptNumber();

        $this->assertEquals('RCPT-2025-0001', $receipt);
    }

    /** @test */
    public function it_sets_next_march_expiry_to_the_upcoming_march_when_today_is_before_april()
    {
        Carbon::setTestNow(Carbon::create(2025, 2, 15, 12, 0, 0));

        $expiry = PaymentService::nextMarchExpiry();

        $this->assertTrue($expiry->equalTo(Carbon::create(2026, 3, 31, 0, 0, 0)));
    }

    /** @test */
    public function it_skips_to_the_following_year_when_today_is_after_march_for_next_march_expiry()
    {
        Carbon::setTestNow(Carbon::create(2025, 4, 1, 9, 0, 0));

        $expiry = PaymentService::nextMarchExpiry();

        $this->assertTrue($expiry->equalTo(Carbon::create(2026, 3, 31, 0, 0, 0)));
    }

    /** @test */
    public function it_extends_from_a_future_membership_expiry_date()
    {
        Carbon::setTestNow(Carbon::create(2025, 2, 15, 12, 0, 0));

        $expiry = PaymentService::nextMarchExpiry(Carbon::create(2026, 3, 31, 0, 0, 0));

        $this->assertTrue($expiry->equalTo(Carbon::create(2027, 3, 31, 0, 0, 0)));
    }

    /** @test */
    public function it_uses_today_when_current_expiry_is_null_or_already_past()
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 10, 8, 0, 0));

        $expiryFromNull = PaymentService::nextMarchExpiry();
        $expiryFromPast = PaymentService::nextMarchExpiry(Carbon::create(2024, 3, 31, 0, 0, 0));

        $expected = Carbon::create(2026, 3, 31, 0, 0, 0);

        $this->assertTrue($expiryFromNull->equalTo($expected));
        $this->assertTrue($expiryFromPast->equalTo($expected));
    }

    /** @test */
    public function it_calculates_the_ten_percent_penalty_and_rounds_to_two_decimals()
    {
        $this->assertSame(10.0, PaymentService::calculatePenalty(100.00));
        $this->assertSame(12.56, PaymentService::calculatePenalty(125.55));
        $this->assertSame(0.0, PaymentService::calculatePenalty(0.0));
    }

    private function createMembership(): Membership
    {
        $user = User::query()->create([
            'name' => 'Test Member',
            'email' => 'member-'.uniqid()."@example.test",
            'password' => bcrypt('password'),
        ]);

        $category = MembershipCategory::query()->create([
            'name' => 'Test Category '.uniqid(),
            'annual_fee' => 100,
            'voting_rights' => true,
            'eligibility_criteria' => 'Test criteria',
        ]);

        return Membership::query()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'status' => Membership::STATUS_APPROVED,
        ]);
    }
}
