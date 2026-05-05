<?php
namespace App\Services;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MembershipService
{

public function generateMemberId(Membership $membership): string
    {
        $code = $this->categoryCode($membership->category->name);
        $year = now()->year;

return DB::transaction(function () use ($membership, $code, $year) {
            $count = Membership::where('status', Membership::STATUS_APPROVED)
                ->whereYear('updated_at', $year)
                ->whereHas('category', fn ($q) => $q->where('name', $membership->category->name))
                ->lockForUpdate()
                ->count();

$sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $memberId = "CCHPL-{$code}-{$year}-{$sequence}";

$membership->update(['member_id' => $memberId]);
            return $memberId;
        });
    }

public function isPenaltyApplicable(Membership $membership): bool
    {
        if (!$membership->isExpired()) {
            return false;
        }

$dueDate = Carbon::create($membership->expiry_date->year, 3, 31);

return now()->greaterThan($dueDate);
    }

public static function calculateOutstandingBalance(Membership $membership): float
    {
        $balance = 0.0;
        if ($membership->isExpired() || $membership->status === 'suspended') {
            $balance = (float) $membership->category->annual_fee;
            if ($membership->isPenaltyApplicable()) {
                $balance += $balance * 0.10;
            }
        }
        return $balance;
    }

public static function categoryCode(string $categoryName): string
    {
        return match (true) {
            str_contains(strtolower($categoryName), 'professional') => 'PRO',
            str_contains(strtolower($categoryName), 'associate')    => 'ASC',
            str_contains(strtolower($categoryName), 'student')      => 'STU',
            str_contains(strtolower($categoryName), 'corporate')    => 'COR',
            str_contains(strtolower($categoryName), 'honorary')     => 'HON',
            default                                                 => 'MEM',
        };
    }
}
