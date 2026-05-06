<?php
namespace App\Services;

use App\Models\Membership;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipService
{
    /**
     * Generate a unique member ID for a membership.
     *
     * @param Membership $membership The membership instance.
     * @return string The generated member ID.
     */
    public function generateMemberId(Membership $membership): string
    {
        $code = self::categoryCode($membership->category->name);
        $year = now()->year;

        return DB::transaction(function () use ($membership, $code, $year) {
            // Count approved memberships for the year and category
            $count = Membership::where('status', Membership::STATUS_APPROVED)
                ->whereYear('updated_at', $year)
                ->whereHas('category', fn ($q) => $q->where('name', $membership->category->name))
                ->lockForUpdate()
                ->count();

            // Format member ID
            $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $memberId = "CCHPL-{$code}-{$year}-{$sequence}";

            $membership->update(['member_id' => $memberId]);
            return $memberId;
        });
    }

    /**
     * Determine if a penalty is applicable for a membership.
     *
     * @param Membership $membership The membership instance.
     * @return bool True if penalty applies, false otherwise.
     */
    public function isPenaltyApplicable(Membership $membership): bool
    {
        if (!$membership->isExpired()) {
            return false;
        }

        // Penalty applies if past March 31 of expiry year
        $dueDate = Carbon::create($membership->expiry_date->year, 3, 31);

        return now()->greaterThan($dueDate);
    }

    /**
     * Calculate the outstanding balance for a membership.
     *
     * @param Membership $membership The membership instance.
     * @return float The outstanding balance.
     */
    public static function calculateOutstandingBalance(Membership $membership): float
    {
        $balance = 0.0;
        if ($membership->isExpired() || $membership->status === 'suspended') {
            $balance = (float) $membership->category->annual_fee;
            // Use instance method for penalty check
            if ((new self)->isPenaltyApplicable($membership)) {
                $balance += $balance * 0.10;
            }
        }
        return $balance;
    }

    /**
     * Get the code for a membership category name.
     *
     * @param string $categoryName The category name.
     * @return string The code for the category.
     */
    public static function categoryCode(string $categoryName): string
    {
        return match (true) {
            str_contains(strtolower($categoryName), 'professional') => 'PRO',
            str_contains(strtolower($categoryName), 'associate')    => 'ASC',
            str_contains(strtolower($categoryName), 'student')      => 'STU',
            str_contains(strtolower($categoryName), 'corporate')    => 'COR',
            default                                                 => 'MEM',
        };
    }
}
