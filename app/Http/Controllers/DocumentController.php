<?php
namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use App\Services\DocumentService;
use Illuminate\Http\Request;

/**
 * Handles generation and download of all formal CCHPL documents.
 *
 * This controller handles automatically generated documents like certificates and receipts.
 * Documents requiring manual composition and review (AGM Notices, EC Minutes)
 * are handled by the DocumentReviewController.
 */
class DocumentController extends Controller
{
    // ──────────────────────────────────────────────
    // Membership Certificate (CCHPL-MEM-002)
    // Members and admins may download their own.
    // ──────────────────────────────────────────────

    public function certificate(Membership $membership)
    {
        // Members may only download their own certificate.
        if (!auth()->user()->hasAnyRole(['super_admin', 'membership_admin'])) {
            abort_unless($membership->user_id === auth()->id(), 403);
        }

        // Certificate only available for approved members.
        abort_unless($membership->status === 'approved', 403, 'Certificate not available until membership is approved.');

        $name = strtolower(str_replace(' ', '-', $membership->user->name));

        return DocumentService::membershipCertificate($membership)
            ->download("cchpl-certificate-{$name}.pdf");
    }

    // ──────────────────────────────────────────────
    // Official Receipt (CCHPL-FIN-003)
    // Members may download receipts for their own payments.
    // ──────────────────────────────────────────────

    public function receipt(Payment $payment)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'payment_admin', 'reports_admin'])) {
            abort_unless(
                $payment->membership->user_id === auth()->id(),
                403
            );
        }

        abort_unless($payment->status === 'verified', 403, 'Receipt only available for verified payments.');

        return DocumentService::officialReceipt($payment)
            ->download("cchpl-receipt-{$payment->receipt_number}.pdf");
    }

    // ──────────────────────────────────────────────
    // Welcome Pack (CCHPL-MEM-001)
    // ──────────────────────────────────────────────

    public function welcomePack(Membership $membership)
    {
        if (!auth()->user()->hasAnyRole(['super_admin', 'membership_admin'])) {
            abort_unless($membership->user_id === auth()->id(), 403);
        }

        abort_unless($membership->status === 'approved', 403, 'Welcome pack not available until membership is approved.');

        $name = strtolower(str_replace(' ', '-', $membership->user->name));

        return DocumentService::welcomePack($membership)
            ->download("cchpl-welcome-pack-{$name}.pdf");
    }
}
