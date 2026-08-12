@extends('layouts.admin')
@section('title', 'Review: ' . \App\Models\DocumentReview::typeLabel($review->type))

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <a href="{{ route('admin.documents.queue') }}" class="text-decoration-none text-muted small">&larr; Back to queue</a>
        <h1 class="h3 fw-bold mt-2 mb-1">{{ \App\Models\DocumentReview::typeLabel($review->type) }}</h1>
        <p class="text-muted small mt-1">
            Ref: {{ \App\Models\DocumentReview::typeRef($review->type) }} &nbsp;|&nbsp;
            Queued: {{ $review->created_at->format('d M Y H:i') }}
            @if($review->creator) by {{ $review->creator->name }} @endif
        </p>
    </div>
    <span class="badge {{ $review->statusBadgeClass() }}">
        {{ ucwords(str_replace('_', ' ', $review->status)) }}
    </span>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span>Document data</span>
                @if (!$review->isSent() && !$review->isCancelled())
                    <span class="small text-muted">Edit any field, then re-preview before sending</span>
                @endif
            </div>
            <div class="card-body">
                @if ($review->isSent() || $review->isCancelled())
                    <dl class="row mb-0 small">
                        @foreach ($review->data as $key => $value)
                            @if (!is_array($value))
                                <dt class="col-4 text-muted fw-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="col-8">{{ $value }}</dd>
                            @endif
                        @endforeach
                    </dl>
                @else
                    <form method="POST" action="{{ route('admin.documents.update', $review) }}" id="edit-form">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            @foreach ($review->data as $key => $value)
                                @if (!is_array($value))
                                    <div class="col-12">
                                        <label for="data-{{ $key }}" class="form-label fw-semibold small text-muted">
                                            {{ ucwords(str_replace(['_', 'Id', 'No'], [' ', ' ID', ' No.'], $key)) }}
                                        </label>
                                        @if (strlen((string)$value) > 80)
                                            <textarea name="data[{{ $key }}]" id="data-{{ $key }}" rows="3" class="form-control">{{ $value }}</textarea>
                                        @else
                                            <input type="text" name="data[{{ $key }}]" id="data-{{ $key }}" value="{{ $value }}" class="form-control">
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                            <div class="col-12">
                                <label for="reviewer-notes" class="form-label fw-semibold small text-muted">Reviewer notes (internal only)</label>
                                <textarea name="reviewer_notes" id="reviewer-notes" rows="2" class="form-control" placeholder="Optional notes visible only to admins">{{ $review->reviewer_notes }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <button type="button" onclick="refreshPreview()" class="btn btn-primary">Save + refresh preview</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Recipient</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted fw-semibold">Name:</dt>
                    <dd class="col-8">{{ $review->recipient_name ?? '—' }}</dd>
                    <dt class="col-4 text-muted fw-semibold">Email:</dt>
                    <dd class="col-8">{{ $review->recipient_email ?? $review->recipient_type }}</dd>
                    <dt class="col-4 text-muted fw-semibold">Send to:</dt>
                    <dd class="col-8">
                        @if ($review->recipient_type === 'all_paid_up_members') All paid-up members
                        @elseif ($review->recipient_type === 'ec_members') EC members
                        @else {{ $review->recipient_name ?? $review->recipient_email }}
                        @endif
                    </dd>
                </dl>
            </div>
        </div>

        @if (!$review->isSent() && !$review->isCancelled())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Actions</div>
            <div class="card-body">
                @if ($review->isPendingReview())
                <form method="POST" action="{{ route('admin.documents.approve', $review) }}" class="mb-3">
                    @csrf
                    <p class="small text-muted mb-3">
                        Mark this document as reviewed and correct. It will move to <strong>Approved</strong> status
                        but will NOT be sent yet — you confirm sending separately.
                    </p>
                    <button type="submit" class="btn btn-primary w-100">
                        Approve document
                    </button>
                </form>
                @endif

                <form method="POST" action="{{ route('admin.documents.send', $review) }}"
                      onsubmit="return confirm('Send this document to {{ addslashes($review->recipient_name ?? $review->recipient_type) }}? This cannot be undone.')">
                    @csrf
                    <p class="small text-muted mb-3">
                        Generate the final PDF and email it to the recipient(s). Make sure you have previewed and approved it first.
                    </p>
                    <div class="form-check d-flex align-items-start gap-2 mb-3">
                        <input type="checkbox" name="confirm_send" value="1" required class="form-check-input mt-1">
                        <label class="form-check-label small">
                            I have previewed this document and confirm the content is correct and ready to send.
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        Send document
                    </button>
                </form>

                <details class="mt-3">
                    <summary class="text-decoration-none text-danger small">Cancel this document</summary>
                    <form method="POST" action="{{ route('admin.documents.cancel', $review) }}" class="mt-3">
                        @csrf
                        <label for="cancellation-reason" class="form-label fw-semibold small text-muted">Reason for cancellation</label>
                        <input type="text" name="cancellation_reason" id="cancellation-reason" required class="form-control mb-3" placeholder="e.g. Duplicate document, wrong member, etc.">
                        <button type="submit" class="btn btn-danger">Confirm cancellation</button>
                    </form>
                </details>
            </div>
        </div>
        @endif

        @if ($review->isSent())
        <div class="alert alert-success">
            <div class="fw-semibold mb-1">Document sent</div>
            <div class="small">
                Sent {{ $review->sent_at->format('d M Y \a\t H:i') }}
                @if($review->sender) by {{ $review->sender->name }} @endif
            </div>
        </div>
        @endif

        @if ($review->isCancelled())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Document cancelled</div>
            <div class="small">{{ $review->cancellation_reason }}</div>
        </div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="position: sticky; top: 1.5rem;">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span>PDF Preview</span>
                <div class="d-flex gap-2">
                    <button onclick="refreshPreview()" class="btn btn-sm btn-primary">Refresh</button>
                    <a href="{{ route('admin.documents.preview', $review) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open in new tab</a>
                </div>
            </div>
            <div id="preview-container" class="relative" style="height: 700px;">
                <div id="preview-loading"
                    class="absolute inset-0 d-flex align-items-center justify-content-center text-muted small bg-white z-10"
                    style="display:none!important">
                    Loading preview...
                </div>
                <iframe
                    id="preview-frame"
                    src="{{ route('admin.documents.preview', $review) }}"
                    class="w-100 h-100 border-0"
                    title="Document preview">
                </iframe>
            </div>
            <div class="card-footer bg-light text-muted small">
                This is a live preview. Save your changes then click Refresh to see the updated PDF.
            </div>
        </div>
    </div>
</div>

<script>
function refreshPreview() {
  const form = document.getElementById('edit-form');
  if (form) {
    const submitBtn = form.querySelector('[type=submit]');
    if (submitBtn) submitBtn.click();
  }

  const frame = document.getElementById('preview-frame');
  if (frame) {
    const loading = document.getElementById('preview-loading');
    if (loading) loading.style.display = 'flex';
    frame.onload = () => { if (loading) loading.style.display = 'none'; };
    frame.src = frame.src.split('?')[0] + '?t=' + Date.now();
  }
}
</script>
@endsection
