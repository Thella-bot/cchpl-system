<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipDocument;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class DocumentUploadController extends Controller
{
    public function downloadMembershipDocument(Membership $membership, MembershipDocument $document)
    {
        $document->loadMissing('membership');

        abort_unless($document->membership_id === $membership->id, 404);

        $user = auth()->user();
        abort_unless($user, 401);

        // Super admins / admins who manage memberships can access any document.
        if ($user->hasAnyRole(['super_admin', 'membership_admin'])) {
            abort_unless(
                in_array($membership->status, [
                    Membership::STATUS_APPROVED,
                    Membership::STATUS_PENDING,
                    'approved',
                    'pending',
                ], true),
                403
            );
        } else {
            // Members can only access their own application documents.
            abort_unless($membership->user_id === $user->id, 403);
        }

        abort_unless($document->file_path, 404);

        // Store path must be under a private disk (local by default).
        // MembershipDocument.file_path should be the value returned by Storage::put/storeAs.
        $path = $document->file_path;

        return Storage::disk('local')->response($path, $document->original_name ?? basename($path));
    }

    public function downloadPaymentProof(Payment $payment)
    {
        $user = auth()->user();
        abort_unless($user, 401);

        $payment->loadMissing(['membership.user']);

        // Allowed: finance_admin/reports_admin/super_admin, or the owner member.
        $canAccess = $user->hasAnyRole(['super_admin', 'finance_admin', 'reports_admin']) ||
            ($payment->membership && $payment->membership->user_id === $user->id);

        abort_unless($canAccess, 403);

        abort_unless($payment->proof_file, 404);

        return Storage::disk('local')->response(
            $payment->proof_file,
            $payment->receipt_number ? ($payment->receipt_number.'.jpg') : 'payment-proof'
        );
    }
}
