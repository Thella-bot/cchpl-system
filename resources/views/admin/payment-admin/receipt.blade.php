use App\Presenters\StatusPresenter;

@extends('layouts.admin')

@section('title', 'Payment Receipt')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <a href="{{ route('admin.payments.verified') }}" class="text-decoration-none text-muted small">
            <i class="fas fa-arrow-left me-1"></i>Back to Verified Payments
        </a>
        <h1 class="h3 fw-bold mt-2 mb-0">Payment Receipt</h1>
    </div>
    <button onclick="window.print(); return false;" class="btn btn-primary">
        <i class="fas fa-print me-1"></i>Print Receipt
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h2 class="h4 fw-bold mb-1" style="color: #1a6b3c;">CCHPL</h2>
                <p class="text-muted small mb-0">Council for Culinary and Hospitality Professionals Lesotho</p>
            </div>
            <div class="text-end">
                <p class="mb-1"><strong>Receipt #</strong></p>
                <p class="font-monospace fw-bold fs-5 mb-0">{{ $payment->receipt_number ?? $payment->transaction_reference }}</p>
            </div>
        </div>

        <!-- Member & Payment Details -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-uppercase small text-muted mb-3">Member Details</h6>
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted">Name</dt>
                    <dd class="col-8 fw-semibold">{{ $payment->membership->user->name }}</dd>
                    <dt class="col-4 text-muted">Email</dt>
                    <dd class="col-8">{{ $payment->membership->user->email }}</dd>
                    <dt class="col-4 text-muted">Phone</dt>
                    <dd class="col-8">{{ $payment->membership->user->phone ?? '—' }}</dd>
                    <dt class="col-4 text-muted">Category</dt>
                    <dd class="col-8">{{ $payment->membership->category->name }}</dd>
                    <dt class="col-4 text-muted">Expiry</dt>
                    <dd class="col-8">{{ $payment->membership->expiry_date?->format('F j, Y') ?? '—' }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-uppercase small text-muted mb-3">Payment Details</h6>
                <dl class="row mb-0 small">
                    <dt class="col-4 text-muted">Amount</dt>
                    <dd class="col-8 fw-bold fs-5 text-success">M{{ number_format($payment->amount, 2) }}</dd>
                    <dt class="col-4 text-muted">Provider</dt>
                    <dd class="col-8">{{ ucfirst($payment->provider) }}</dd>
                    <dt class="col-4 text-muted">Reference</dt>
                    <dd class="col-8 font-monospace">{{ $payment->transaction_reference }}</dd>
                    <dt class="col-4 text-muted">Status</dt>
                    <dd class="col-8">
                        <span class="badge {{ StatusPresenter::paymentStatusBadge($payment->status) }}">{{ ucfirst($payment->status) }}</span>
                    </dd>
                    @if($payment->verified_at)
                        <dt class="col-4 text-muted">Verified</dt>
                        <dd class="col-8">{{ $payment->verified_at->format('F j, Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($payment->verification_notes)
            <div class="p-3 bg-light rounded mb-4">
                <h6 class="fw-bold small text-muted mb-1">Verification Notes</h6>
                <p class="mb-0 small">{{ $payment->verification_notes }}</p>
            </div>
        @endif

        <div class="border-top pt-3">
            <p class="small text-muted mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Thank you for supporting CCHPL. This receipt is generated for your records.
            </p>
        </div>
    </div>
</div>
@endsection
