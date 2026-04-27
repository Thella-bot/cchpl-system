<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentReview;
use App\Services\DocumentReviewService;
use App\Services\Documents\AgmNoticeDocument;
use App\Services\Documents\EcMinutesDocument;
use Illuminate\Http\Request;

class DocumentReviewController extends Controller
{
    protected $documentReviewService;

    public function __construct(DocumentReviewService $documentReviewService)
    {
        $this->documentReviewService = $documentReviewService;
    }

    public function queue(Request $request)
    {
        $query = DocumentReview::whereIn('type', [
                DocumentReview::TYPE_AGM_NOTICE,
                DocumentReview::TYPE_EC_MINUTES,
            ])
            ->with('creator')
            ->orderByRaw("FIELD(status, 'pending_review', 'approved', 'sent', 'cancelled')")
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $reviews      = $query->paginate(20)->withQueryString();
        $pendingCount = DocumentReview::whereIn('type', [
            DocumentReview::TYPE_AGM_NOTICE,
            DocumentReview::TYPE_EC_MINUTES,
        ])->where('status', DocumentReview::STATUS_PENDING_REVIEW)->count();

        return view('admin.document-review.queue', compact('reviews', 'pendingCount'));
    }

    public function show(DocumentReview $review)
    {
        $this->documentReviewService->assertEditableType($review);
        $review->load('creator', 'reviewer', 'sender');
        return view('admin.document-review.review', compact('review'));
    }

    public function update(Request $request, DocumentReview $review)
    {
        $this->documentReviewService->assertEditableType($review);
        abort_if($review->isSent() || $review->isCancelled(), 403, 'Document already finalised.');

        $validated = $request->validate([
            'data'           => 'required|array',
            'reviewer_notes' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return back()->with('success', '✅ Changes saved. Refresh the preview to confirm.');
    }

    public function preview(DocumentReview $review, AgmNoticeDocument $agmNoticeDocument, EcMinutesDocument $ecMinutesDocument)
    {
        return $this->documentReviewService->buildPdf($review->type, $review->data, $agmNoticeDocument, $ecMinutesDocument)
            ->stream($this->documentReviewService->filename($review) . '_PREVIEW.pdf');
    }

    public function previewDraft(Request $request, AgmNoticeDocument $agmNoticeDocument, EcMinutesDocument $ecMinutesDocument)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $request->validate(['type' => 'required|string', 'data' => 'required|array']);
        $this->documentReviewService->assertEditableType((object)['type' => $request->type]);

        return $this->documentReviewService->buildPdf($request->type, $request->data, $agmNoticeDocument, $ecMinutesDocument)
            ->stream('DRAFT_PREVIEW.pdf');
    }

    public function approve(Request $request, DocumentReview $review)
    {
        $this->documentReviewService->approve($request, $review);

        return back()->with('success', '✅ Document approved. Click Send when ready.');
    }

    public function send(Request $request, DocumentReview $review, AgmNoticeDocument $agmNoticeDocument, EcMinutesDocument $ecMinutesDocument)
    {
        $this->documentReviewService->send($request, $review, $agmNoticeDocument, $ecMinutesDocument);

        return redirect()
            ->route('admin.documents.queue')
            ->with('success', "✅ {$review->typeLabel()} sent to {$review->recipient_name ?? $review->recipient_type}.");
    }

    public function cancel(Request $request, DocumentReview $review)
    {
        $this->documentReviewService->cancel($request, $review);

        return redirect()
            ->route('admin.documents.queue')
            ->with('success', 'Document cancelled.');
    }
}
