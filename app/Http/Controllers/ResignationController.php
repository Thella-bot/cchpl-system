<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Membership;
use App\Models\Resignation;
use App\Services\MembershipService;
use Illuminate\Http\Request;

class ResignationController extends Controller
{
    // ──────────────────────────────────────────────
    // Member: show resignation form
    // ──────────────────────────────────────────────

    public function create()
    {
        $membership = Membership::where('user_id', auth()->id())
            ->whereIn('status', [Membership::STATUS_APPROVED, Membership::STATUS_SUSPENDED, Membership::STATUS_EXPIRED])
            ->with('category')
            ->latest()
            ->first();

        if (!$membership) {
            return redirect()->route('member.dashboard')
                ->with('error', 'You do not have an active membership to resign from.');
        }

        // Prevent duplicate pending resignations
        $existing = Resignation::where('user_id', auth()->id())
            ->where('status', Resignation::STATUS_PENDING)
            ->exists();

        if ($existing) {
            return redirect()->route('member.dashboard')
                ->with('error', 'You already have a pending resignation on file. Please contact the Secretary if you wish to withdraw it.');
        }

        return view('member.resign', [
            'membership'   => $membership,
            'balance'      => MembershipService::calculateOutstandingBalance($membership),
            'reasonCodes'  => Resignation::REASON_CODES,
        ]);
    }

    // ──────────────────────────────────────────────
    // Member: submit resignation (Part A)
    // ──────────────────────────────────────────────

    public function store(Request $request)
    {
        $membership = Membership::where('user_id', auth()->id())
            ->whereIn('status', [Membership::STATUS_APPROVED, Membership::STATUS_SUSPENDED, Membership::STATUS_EXPIRED])
            ->with('category')
            ->latest()
            ->first();

        abort_unless($membership, 403, 'No active membership found.');

        $validated = $request->validate([
            'effective_date' => 'required|date|after_or_equal:today',
            'reason_code'    => 'nullable|in:' . implode(',', array_keys(Resignation::REASON_CODES)),
            'reason_notes'   => 'nullable|string|max:500',
            'confirm'        => 'required|accepted',
        ], [
            'effective_date.after_or_equal' => 'The effective date must be today or a future date.',
            'confirm.accepted'              => 'You must confirm that you understand membership benefits will cease.',
        ]);

        $resignation = Resignation::create([
            'user_id'             => auth()->id(),
            'membership_id'       => $membership->id,
            'status'              => Resignation::STATUS_PENDING,
            'effective_date'      => $validated['effective_date'],
            'reason_code'         => $validated['reason_code'] ?? null,
            'reason_notes'        => $validated['reason_notes'] ?? null,
            'balance_outstanding' => MembershipService::calculateOutstandingBalance($membership),
        ]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'membership.resignation.submitted',
            'auditable_type' => Resignation::class,
            'auditable_id'   => $resignation->id,
            'old_values'     => [],
            'new_values'     => $resignation->only(['status', 'effective_date', 'reason_code']),
            'meta'           => ['submitted_by' => auth()->user()->email],
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', '✅ Your resignation has been submitted. The Secretary will acknowledge it within 14 days per the CCHPL Bylaws.');
    }
}
