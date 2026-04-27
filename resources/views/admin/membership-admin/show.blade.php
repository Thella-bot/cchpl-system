@extends('layouts.admin')

@section('title', 'Application — ' . $membership->user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.memberships.index') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i>Back to Pending Applications
    </a>
    <h1 class="h3 fw-bold mt-2 mb-0">Membership Application</h1>
</div>

<!-- Info Grid -->
<div class="row g-4 mb-4">
    <!-- Applicant -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-user text-muted me-2"></i>Applicant Information
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7 fw-semibold">{{ $membership->user->name }}</dd>
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $membership->user->email }}</dd>
                    <dt class="col-5 text-muted">Phone</dt>
                    <dd class="col-7">{{ $membership->user->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Organisation</dt>
                    <dd class="col-7">{{ $membership->user->organization ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Membership Details -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-id-card text-muted me-2"></i>Membership Details
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-6 text-muted">Category</dt>
                    <dd class="col-6 fw-semibold">{{ $membership->category->name }}</dd>
                    <dt class="col-6 text-muted">Annual Fee</dt>
                    <dd class="col-6 fw-semibold">M{{ number_format($membership->category->annual_fee, 2) }}</dd>
                    <dt class="col-6 text-muted">Status</dt>
                    <dd class="col-6">
                        <span class="badge {{ $membership->statusBadgeClass() }}">
                            {{ ucfirst($membership->status) }}
                        </span>
                    </dd>
                    <dt class="col-6 text-muted">Member ID</dt>
                    <dd class="col-6">{{ $membership->member_id ?? '—' }}</dd>
                    <dt class="col-6 text-muted">Expires</dt>
                    <dd class="col-6">{{ $membership->expiry_date?->format('M d, Y') ?? '—' }}</dd>
                    <dt class="col-6 text-muted">Applied</dt>
                    <dd class="col-6">{{ $membership->created_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Category Requirements -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-list-check text-muted me-2"></i>Requirements
            </div>
            <div class="card-body small">
                <p class="text-muted mb-1">Eligibility</p>
                <p class="mb-3">{{ $membership->category->eligibility_criteria ?? '—' }}</p>
                <p class="text-muted mb-1">Voting Rights</p>
                <p class="mb-3">
                    @if($membership->category->voting_rights)
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-secondary">No</span>
                    @endif
                </p>
                @if($membership->category->other_notes)
                    <p class="text-muted mb-1">Notes</p>
                    <p class="mb-0">{{ $membership->category->other_notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Documents -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">
        <i class="fas fa-folder-open text-muted me-2"></i>Uploaded Documents
    </div>
    <div class="card-body">
        @if ($membership->documents->isEmpty())
            <p class="text-muted mb-0">No documents uploaded.</p>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($membership->documents as $doc)
                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <div class="fw-semibold">{{ $doc->document_type }}</div>
                                <div class="small text-muted">{{ $doc->original_name }}</div>
                                <span class="badge mt-1
                                    {{ $doc->status === 'approved' ? 'bg-success' : ($doc->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($doc->status) }}
                                </span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <form method="POST" action="{{ route('admin.memberships.document.review', [$membership->id, $doc->id]) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#doc-reject-{{ $doc->id }}">
                                    <i class="fas fa-times me-1"></i>Reject
                                </button>
                            </div>
                        </div>
                        <div class="collapse mt-2" id="doc-reject-{{ $doc->id }}">
                            <form method="POST" action="{{ route('admin.memberships.document.review', [$membership->id, $doc->id]) }}">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="reason" class="form-control"
                                           placeholder="Reason for rejection (optional)…">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Payment History -->
@if ($membership->payments->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-credit-card text-muted me-2"></i>Payment History
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membership->payments as $payment)
                            <tr>
                                <td class="font-monospace small">{{ $payment->transaction_reference }}</td>
                                <td class="fw-semibold">M{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->provider) }}</td>
                                <td>
                                    <span class="badge {{ $payment->statusBadgeClass() }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $payment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Approve / Reject Actions -->
@if($membership->status === 'pending')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-gavel text-muted me-2"></i>Decision
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('admin.memberships.approve', $membership->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Approve application for {{ addslashes($membership->user->name) }}?')">
                            <i class="fas fa-check me-2"></i>Approve Application
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.memberships.reject', $membership->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="reason" class="form-control" required minlength="10"
                                   placeholder="Rejection reason (required, min 10 chars)…">
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>Reject Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
