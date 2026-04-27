<?php
namespace App\Services;

use App\Models\AuditLog;
use App\Models\DocumentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Membership;
use App\Models\User;
use App\Services\Documents\AgmNoticeDocument;
use App\Services\Documents\EcMinutesDocument;

class DocumentReviewService
{
    public function approve(Request $request, DocumentReview $review)
    {
        $this->assertEditableType($review);
        abort_unless($review->isPendingReview(), 403, 'Only pending documents can be approved.');

        $review->update([
            'status'         => DocumentReview::STATUS_APPROVED,
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
            'reviewer_notes' => $request->reviewer_notes ?? $review->reviewer_notes,
        ]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'document_review.approved',
            'auditable_type' => DocumentReview::class,
            'auditable_id'   => $review->id,
            'old_values'     => ['status' => DocumentReview::STATUS_PENDING_REVIEW],
            'new_values'     => ['status' => DocumentReview::STATUS_APPROVED],
            'meta'           => ['approved_by' => auth()->user()->email ?? null],
        ]);

        return $review;
    }

    public function send(Request $request, DocumentReview $review, AgmNoticeDocument $agmNoticeDocument, EcMinutesDocument $ecMinutesDocument)
    {
        $this->assertEditableType($review);
        abort_if($review->isSent() || $review->isCancelled(), 403, 'Document already finalised.');

        // Prevent timeouts for large distribution lists
        set_time_limit(300);

        $request->validate(['confirm_send' => 'required|accepted']);

        $filename = $this->filename($review) . '.pdf';
        $tmpDir   = storage_path('app/tmp');

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $tmpPath = $tmpDir . '/' . $filename;
        $this->buildPdf($review->type, $review->data, $agmNoticeDocument, $ecMinutesDocument)->save($tmpPath);

        $this->dispatchEmail($review, $tmpPath, $filename);

        @unlink($tmpPath);

        $review->update([
            'status'  => DocumentReview::STATUS_SENT,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
        ]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'document_review.sent',
            'auditable_type' => DocumentReview::class,
            'auditable_id'   => $review->id,
            'old_values'     => ['status' => $review->getOriginal('status')],
            'new_values'     => ['status' => DocumentReview::STATUS_SENT, 'sent_at' => now()],
            'meta'           => [
                'sent_by'        => auth()->user()->email ?? null,
                'recipient_type' => $review->recipient_type,
                'filename'       => $filename,
            ],
        ]);

        return $review;
    }

    public function cancel(Request $request, DocumentReview $review)
    {
        $this->assertEditableType($review);
        abort_if($review->isSent(), 403, 'A sent document cannot be cancelled.');

        $request->validate(['cancellation_reason' => 'required|string|min:5']);

        $review->update([
            'status'              => DocumentReview::STATUS_CANCELLED,
            'cancellation_reason' => $request->cancellation_reason,
            'reviewed_by'         => auth()->id(),
            'reviewed_at'         => now(),
        ]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'document_review.cancelled',
            'auditable_type' => DocumentReview::class,
            'auditable_id'   => $review->id,
            'old_values'     => ['status' => $review->getOriginal('status')],
            'new_values'     => ['status' => DocumentReview::STATUS_CANCELLED],
            'meta'           => [
                'cancelled_by' => auth()->user()->email ?? null,
                'reason'       => $request->cancellation_reason,
            ],
        ]);

        return $review;
    }

    private function buildPdf(string $type, array $data, AgmNoticeDocument $agmNoticeDocument, EcMinutesDocument $ecMinutesDocument)
    {
        return match ($type) {
            DocumentReview::TYPE_AGM_NOTICE => $agmNoticeDocument->build($data),
            DocumentReview::TYPE_EC_MINUTES => $ecMinutesDocument->build($data),
            default                         => abort(400, "Unsupported review type: {$type}"),
        };
    }

    private function filename(DocumentReview $review): string
    {
        $slug = strtolower(str_replace([' ', '/'], '-', $review->data['meetingNo'] ?? $review->type));
        return 'cchpl-' . str_replace('_', '-', $review->type) . '-' . $slug . '-' . now()->format('Ymd');
    }

    private function assertEditableType(DocumentReview $review): void
    {
        abort_unless(
            in_array($review->type, [DocumentReview::TYPE_AGM_NOTICE, DocumentReview::TYPE_EC_MINUTES]),
            403,
            'This document type is handled automatically and does not go through the review queue.'
        );
    }

    private function dispatchEmail(DocumentReview $review, string $pdfPath, string $filename): void
    {
        $typeLabel = $review->typeLabel();

        if ($review->recipient_type === DocumentReview::RECIPIENT_ALL_PAID_UP) {
            // Use chunking to prevent memory exhaustion
            Membership::where('status', 'approved')
                ->whereNotNull('expiry_date')
                // Fix: use whereDate to include the full expiry day (ignore time)
                ->whereDate('expiry_date', '>=', now())
                ->with('user')
                ->chunk(100, function ($members) use ($typeLabel, $pdfPath, $filename) {
                    foreach ($members as $membership) {
                        try {
                            if (!$membership->user?->email) continue;
                            $this->sendSingleEmail(
                                $membership->user->email,
                                $membership->user->name,
                                $typeLabel,
                                $pdfPath,
                                $filename
                            );
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Failed to send document review email to membership #{$membership->id}: " . $e->getMessage());
                        }
                    }
                });

        } elseif ($review->recipient_type === DocumentReview::RECIPIENT_EC_MEMBERS) {
            $ecMembers = User::whereHas('roles', fn($q) => $q->where('name', 'executive_committee'))->get();
            foreach ($ecMembers as $ec) {
                if (!$ec->email) continue;
                $this->sendSingleEmail($ec->email, $ec->name, $typeLabel, $pdfPath, $filename);
            }
        }
    }

    private function sendSingleEmail($email, $name, $subject, $path, $filename)
    {
        Mail::send([], [], function ($m) use ($email, $name, $subject, $path, $filename) {
            $m->to($email, $name)
              ->subject("CCHPL — {$subject}")
              ->text("Dear {$name},

Please find the {$subject} attached.

Kind regards,
CCHPL Secretary
secretary@cchpl.org.ls")
              ->attach($path, ['as' => $filename, 'mime' => 'application/pdf']);
        });
    }
}
