<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    public function certificate(Membership $membership)
    {

        if (! auth()->user()->hasAnyRole(['super_admin', 'membership_admin'])) {
            abort_unless($membership->user_id === auth()->id(), 403);
        }

        abort_unless($membership->status === 'approved', 403, 'Certificate not available until membership is approved.');

        $name = strtolower(str_replace(' ', '-', $membership->user->name));

        return DocumentService::membershipCertificate($membership)
            ->download("cchpl-certificate-{$name}.pdf");
    }

    public function receipt(Payment $payment)
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'finance_admin', 'reports_admin'])) {
            abort_unless(
                $payment->membership->user_id === auth()->id(),
                403
            );
        }

        abort_unless($payment->status === 'verified', 403, 'Receipt only available for verified payments.');

        return DocumentService::officialReceipt($payment)
            ->download("cchpl-receipt-{$payment->receipt_number}.pdf");
    }

    public function welcomePack(Membership $membership)
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'membership_admin'])) {
            abort_unless($membership->user_id === auth()->id(), 403);
        }

        abort_unless($membership->status === 'approved', 403, 'Welcome pack not available until membership is approved.');

        $name = strtolower(str_replace(' ', '-', $membership->user->name));

        return DocumentService::welcomePack($membership)
            ->download("cchpl-welcome-pack-{$name}.pdf");
    }
}
