@extends('layouts.admin')
@section('title', 'Document Review Queue')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Document Review Queue</h1>
        <p class="text-muted mb-0">Review, edit, preview, and approve documents before they are sent to recipients.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.documents.compose.agm') }}" class="btn btn-primary btn-sm">
            + AGM Notice
        </a>
        <a href="{{ route('admin.documents.compose.minutes') }}" class="btn btn-primary btn-sm">
            + EC Minutes
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="admin-shell-card mb-3">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="status" class="form-label fw-semibold small text-muted">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All statuses</option>
                <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending review</option>
                <option value="approved"       {{ request('status') === 'approved'       ? 'selected' : '' }}>Approved</option>
                <option value="sent"           {{ request('status') === 'sent'           ? 'selected' : '' }}>Sent</option>
                <option value="cancelled"      {{ request('status') === 'cancelled'      ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="type" class="form-label fw-semibold small text-muted">Document type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All types</option>
                @foreach (['receipt','welcome_pack','certificate','agm_notice','ec_minutes'] as $t)
                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>
                        {{ \App\Models\DocumentReview::typeLabel($t) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary me-2">Filter</button>
            <a href="{{ route('admin.documents.queue') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>
</div>

@if ($pendingCount > 0)
    <div class="alert alert-warning d-flex align-items-center gap-3">
        <span class="badge bg-warning text-dark">{{ $pendingCount }}</span>
        <span class="fw-medium">document(s) are waiting for review before they can be sent.</span>
    </div>
@endif

<div class="admin-shell-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Document</th>
                    <th>Recipient</th>
                    <th>Status</th>
                    <th>Queued</th>
                    <th>Sent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr class="{{ $review->isPendingReview() ? 'table-warning' : '' }}">
                        <td class="ps-4 fw-semibold">
                            <div>{{ \App\Models\DocumentReview::typeLabel($review->type) }}</div>
                            <div class="small text-muted">{{ \App\Models\DocumentReview::typeRef($review->type) }}</div>
                        </td>
                        <td>
                            <div>{{ $review->recipient_name ?? '—' }}</div>
                            <div class="small text-muted">{{ $review->recipient_email ?? $review->recipient_type }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $review->statusBadgeClass() }}">
                                {{ ucwords(str_replace('_', ' ', $review->status)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $review->created_at->format('d M Y H:i') }}</td>
                        <td class="small text-muted">{{ $review->sent_at ? $review->sent_at->format('d M Y H:i') : '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.documents.show', $review) }}" class="btn btn-sm btn-primary">
                                @if ($review->isPendingReview()) Review @elseif ($review->isSent()) View @else Open @endif
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            No documents in the queue matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $reviews->withQueryString()->links() }}</div>
</div>
@endsection
