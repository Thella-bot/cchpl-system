@extends('layouts.admin')

@section('title', 'Pending Payment Verifications')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Pending Payment Verifications</h1>
    <p class="text-muted mb-0">Review member payment proofs and verify or reject them.</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-hourglass-half text-warning fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($pendingCount) }}</div>
                    <div class="small text-muted">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-check-circle text-success fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($verifiedCount) }}</div>
                    <div class="small text-muted">Verified</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-times-circle text-danger fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($rejectedCount) }}</div>
                    <div class="small text-muted">Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($payments->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-check-double fa-3x mb-3 text-secondary opacity-50"></i>
            <p class="mb-0 fs-5">No pending payments to verify. All caught up!</p>
        </div>
    </div>
@else
    <div class="d-flex flex-column gap-3">
        @foreach ($payments as $payment)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="fw-semibold mb-1">{{ $payment->membership->user->name }}</h5>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-envelope me-1"></i>{{ $payment->membership->user->email }}
                                &nbsp;·&nbsp;
                                <i class="fas fa-calendar me-1"></i>Submitted {{ $payment->created_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                        <span class="badge bg-warning text-dark">Pending Verification</span>
                    </div>

                    <!-- Payment Info -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-3">
                            <small class="text-muted">Amount</small>
                            <p class="fw-bold fs-5 mb-0 text-success">M{{ number_format($payment->amount, 2) }}</p>
                        </div>
                        <div class="col-sm-3">
                            <small class="text-muted">Provider</small>
                            <p class="fw-semibold mb-0">{{ ucfirst($payment->provider) }}</p>
                        </div>
                        <div class="col-sm-3">
                            <small class="text-muted">Reference</small>
                            <p class="fw-semibold mb-0 font-monospace small">{{ $payment->transaction_reference }}</p>
                        </div>
                        <div class="col-sm-3">
                            <small class="text-muted">Membership</small>
                            <p class="fw-semibold mb-0">{{ $payment->membership->category->name }}</p>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    @if ($payment->proof_file)
                        <div class="mb-3">
                            <small class="text-muted">Payment Proof:</small><br>
                            <a href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank"
                               class="small text-primary">
                                <i class="fas fa-external-link-alt me-1"></i>View Full Image
                            </a>
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $payment->proof_file) }}" alt="Payment Proof"
                                     class="rounded border" style="max-height: 200px; max-width: 400px; object-fit: contain;">
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>No payment proof uploaded yet.
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 flex-wrap mb-2">
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i>Full Details
                        </a>
                        <button class="btn btn-sm btn-success" type="button"
                                data-bs-toggle="collapse" data-bs-target="#verify-{{ $payment->id }}">
                            <i class="fas fa-check me-1"></i>Verify Payment
                        </button>
                        <button class="btn btn-sm btn-danger" type="button"
                                data-bs-toggle="collapse" data-bs-target="#reject-{{ $payment->id }}">
                            <i class="fas fa-times me-1"></i>Reject
                        </button>
                    </div>

                    <!-- Verify Form -->
                    <div class="collapse" id="verify-{{ $payment->id }}">
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="mt-2">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="verification_notes" class="form-control form-control-sm"
                                       placeholder="Verification notes (required, min 5 chars)…" required minlength="5">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-1"></i>Confirm Verify
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Reject Form -->
                    <div class="collapse" id="reject-{{ $payment->id }}">
                        <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" class="mt-2">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="rejection_reason" class="form-control form-control-sm"
                                       placeholder="Rejection reason (required, min 10 chars)…" required minlength="10">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times me-1"></i>Confirm Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>
@endif
@endsection
