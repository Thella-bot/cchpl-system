<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentReview;
use App\Models\Payment;
use App\Notifications\PaymentStatusNotification;
use App\Services\DocumentService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentAdminController extends Controller
{
    public function index()
    {
        $pendingCount  = Payment::where('status', 'pending')->count();
        $verifiedCount = Payment::where('status', 'verified')->count();
        $rejectedCount = Payment::where('status', 'rejected')->count();

        $payments = Payment::where('status', 'pending')
            ->with('membership.user', 'membership.category')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.payment-admin.index', compact(
            'payments', 'pendingCount', 'verifiedCount', 'rejectedCount'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load('membership.user', 'membership.category');
        return view('admin.payment-admin.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'verification_notes' => 'required|string|min:5',
        ]);

        $oldValues = $payment->only(['status', 'verification_notes']);

        PaymentService::verifyPayment($payment, true);
        $payment->refresh();
        $payment->update(['verification_notes' => $request->verification_notes]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'payment.verified',
            'auditable_type' => Payment::class,
            'auditable_id'   => $payment->id,
            'old_values'     => $oldValues,
            'new_values'     => $payment->fresh()->only(['status', 'verification_notes', 'receipt_number']),
            'meta'           => ['verified_by' => auth()->user()->email ?? null],
        ]);

        $membership = $payment->membership->fresh();
        $user       = $membership->user;

        // ── Brief notification email (no attachment) ──────────────────────
        $user->notify(new PaymentStatusNotification(
            $payment, 'verified', $request->verification_notes
        ));

        // ── Financial year label ──────────────────────────────────────────
        $fyYear  = $payment->verified_at->month >= 4
            ? $payment->verified_at->year
            : $payment->verified_at->year - 1;

        // ── Auto-send receipt PDF ─────────────────────────────────────────
        // All data is derived from verified system records — no review needed.
        DocumentService::sendToMember(
            DocumentReview::TYPE_RECEIPT,
            [
                'receiptNo'      => $payment->receipt_number,
                'date'           => $payment->verified_at->format('d F Y'),
                'receivedFrom'   => $user->name,
                'memberId'       => $membership->member_id ?? '—',
                'contact'        => $user->phone ?? '—',
                'paymentFor'     => $membership->category->name . ' Membership Fee',
                'paymentPeriod'  => 'Membership Year ' . $fyYear . '/' . ($fyYear + 1),
                'paymentMethod'  => ucfirst($payment->provider) . ' (Mobile Money)',
                'transactionRef' => $payment->transaction_reference,
                'amount'         => number_format((float) $payment->amount, 2),
                'amountWords'    => DocumentService::amountInWords((float) $payment->amount),
                'balance'        => '0.00',
            ],
            $user->email,
            $user->name,
            auth()->id()
        );

        // ── Auto-send welcome pack on first verified payment ──────────────
        $isFirstPayment = $membership->payments()
            ->where('status', 'verified')
            ->count() === 1;

        if ($isFirstPayment) {
            $expiry = $membership->expiry_date;
            DocumentService::sendToMember(
                DocumentReview::TYPE_WELCOME_PACK,
                [
                    'memberName'   => $user->name,
                    'memberId'     => $membership->member_id ?? '—',
                    'category'     => $membership->category->name,
                    'annualFee'    => 'M' . number_format($membership->category->annual_fee, 2),
                    'votingRights' => $membership->category->voting_rights ? 'Yes' : 'No',
                    'validUntil'   => $expiry
                                        ? '31 March ' . $expiry->year
                                        : '31 March ' . now()->addYear()->year,
                    'dateJoined'   => now()->format('d F Y'),
                    'dateIssued'   => now()->format('d F Y'),
                ],
                $user->email,
                $user->name,
                auth()->id()
            );
        }

        $extra = $isFirstPayment ? ' Receipt and Welcome Pack emailed.' : ' Receipt emailed.';

        return back()->with('success', "✅ Payment verified for {$user->name}.{$extra}");
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $oldValues = $payment->only(['status', 'verification_notes']);

        PaymentService::verifyPayment($payment, false);
        $payment->update(['verification_notes' => $request->rejection_reason]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'payment.rejected',
            'auditable_type' => Payment::class,
            'auditable_id'   => $payment->id,
            'old_values'     => $oldValues,
            'new_values'     => $payment->fresh()->only(['status', 'verification_notes']),
            'meta'           => [
                'rejected_by' => auth()->user()->email ?? null,
                'reason'      => $request->rejection_reason,
            ],
        ]);

        $payment->membership->user->notify(
            new PaymentStatusNotification($payment, 'rejected', $request->rejection_reason)
        );

        return back()->with('success', '❌ Payment rejected. Member has been notified.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load('membership.user', 'membership.category');
        return view('admin.payment-admin.receipt', compact('payment'));
    }

    public function verified()
    {
        $payments = Payment::where('status', 'verified')
            ->with('membership.user', 'membership.category')
            ->orderBy('verified_at', 'desc')
            ->paginate(20);

        return view('admin.payment-admin.verified', compact('payments'));
    }

    public function rejected()
    {
        $payments = Payment::where('status', 'rejected')
            ->with('membership.user', 'membership.category')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.payment-admin.rejected', compact('payments'));
    }
}
