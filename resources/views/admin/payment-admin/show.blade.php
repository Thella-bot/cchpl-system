@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.payments.index') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i>Back to Pending Payments
    </a>
    <h1 class="h3 fw-bold mt-2 mb-0">Payment Details</h1>
</div>

<div class="row g-4">
    <!-- Left column: payment & member info -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-credit-card text-muted me-2"></i>Payment Information
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Transaction Reference</dt>
                    <dd class="col-sm-7 font-monospace">{{ $payment->transaction_reference }}</dd>

                    <dt class="col-sm-5 text-muted">Amount</dt>
                    <dd class="col-sm-7 fw-bold fs-5 text-success">M{{ number_format($payment->amount, 2) }}</dd>

                    <dt class="col-sm-5 text-muted">Provider</dt>
                    <dd class="col-sm-7">{{ ucfirst($payment->provider) }}</dd>

                    <dt class="col-sm-5 text-muted">Purpose</dt>
                    <dd class="col-sm-7">{{ $payment->purpose ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Status</dt>
                    <dd class="col-sm-7">
                        <span class="badge {{ $payment->statusBadgeClass() }}">{{ ucfirst($payment->status) }}</span>
                    </dd>

                    <dt class="col-sm-5 text-muted">Submitted</dt>
                    <dd class="col-sm-7">{{ $payment->created_at->format('M d, Y H:i') }}</dd>

                    @if($payment->verified_at)
                        <dt class="col-sm-5 text-muted">Verified At</dt>
                        <dd class="col-sm-7">{{ $payment->verified_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-5 text-muted">Receipt No.</dt>
                        <dd class="col-sm-7 font-monospace">{{ $payment->receipt_number ?? '—' }}</dd>
                    @endif

                    @if($payment->verification_notes)
                        <dt class="col-sm-5 text-muted">Notes</dt>
                        <dd class="col-sm-7">{{ $payment->verification_notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-user text-muted me-2"></i>Member Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Name</dt>
                    <dd class="col-sm-7 fw-semibold">{{ $payment->membership->user->name }}</dd>

                    <dt class="col-sm-5 text-muted">Email</dt>
                    <dd class="col-sm-7">{{ $payment->membership->user->email }}</dd>

                    <dt class="col-sm-5 text-muted">Phone</dt>
                    <dd class="col-sm-7">{{ $payment->membership->user->phone ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Category</dt>
                    <dd class="col-sm-7">{{ $payment->membership->category->name }}</dd>

                    <dt class="col-sm-5 text-muted">Member ID</dt>
                    <dd class="col-sm-7 font-monospace">{{ $payment->membership->member_id ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Expiry Date</dt>
                    <dd class="col-sm-7">{{ $payment->membership->expiry_date?->format('M d, Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Right column: proof image + actions -->
    <div class="col-lg-5">
        <!-- Proof Image -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-image text-muted me-2"></i>Payment Proof
            </div>
            <div class="card-body">
                @if ($payment->proof_file)
                    <a href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank">
                        <img src="{{ asset('storage/' . $payment->proof_file) }}" alt="Payment Proof"
                             class="img-fluid rounded border">
                    </a>
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank"
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-expand-alt me-1"></i>View Full Size
                        </a>
                    </div>
                @else
                    <p class="text-muted mb-0">No proof file uploaded.</p>
                @endif
            </div>
        </div>

        <!-- Actions (only for pending) -->
        @if($payment->isPending())
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white fw-semibold">
                    <i class="fas fa-check me-2"></i>Verify Payment
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Verification Notes <span class="text-danger">*</span></label>
                            <textarea name="verification_notes" class="form-control form-control-sm" rows="2"
                                      required minlength="5" placeholder="Notes for verification record…"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-2"></i>Confirm Verify
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white fw-semibold">
                    <i class="fas fa-times me-2"></i>Reject Payment
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control form-control-sm" rows="2"
                                      required minlength="10" placeholder="Reason for rejection (min 10 chars)…"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>Confirm Reject
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($payment->isVerified())
            <a href="{{ route('admin.payments.receipt', $payment->id) }}" class="btn btn-outline-primary w-100">
                <i class="fas fa-receipt me-2"></i>View/Print Receipt
            </a>
        @endif
    </div>
</div>
@endsection
