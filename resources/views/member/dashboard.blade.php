@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="row g-4">
    <!-- Welcome Header -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-user text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Welcome, {{ Auth::user()->name }}!</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Council for Culinary and Hospitality Professionals Lesotho
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($membership)
        <!-- Membership Status Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-id-card text-muted me-2"></i>Membership Status
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fs-5 fw-bold">{{ $membership->category->name }}</span>
                        <span class="badge px-3 py-2 {{ $membership->statusBadgeClass() }} fs-6">
                            {{ ucfirst($membership->status) }}
                        </span>
                    </div>

                    @if ($membership->member_id)
                        <div class="p-3 bg-light rounded mb-3">
                            <small class="text-muted">Member ID</small>
                            <p class="fw-bold font-monospace mb-0 fs-5">{{ $membership->member_id }}</p>
                        </div>
                    @endif

                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Annual Fee</dt>
                        <dd class="col-7 fw-semibold">M{{ number_format($membership->category->annual_fee, 2) }}</dd>

                        @if ($membership->expiry_date)
                            <dt class="col-5 text-muted">Expires</dt>
                            <dd class="col-7 fw-semibold {{ $membership->isExpiringSoon() ? 'text-warning' : '' }}
                                {{ $membership->isExpired() ? 'text-danger' : '' }}">
                                {{ $membership->expiry_date->format('d F Y') }}
                                @if ($membership->isExpiringSoon() && !$membership->isExpired())
                                    <span class="badge bg-warning text-dark ms-1">Expiring soon</span>
                                @endif
                                @if ($membership->isExpired())
                                    <span class="badge bg-danger ms-1">Expired</span>
                                @endif
                            </dd>
                        @endif

                        <dt class="col-5 text-muted">Applied</dt>
                        <dd class="col-7">{{ $membership->created_at->format('M d, Y') }}</dd>
                    </dl>

                    @if ($membership->status === 'pending')
                        <div class="alert alert-info py-2 mt-3 mb-0 small">
                            <i class="fas fa-info-circle me-1"></i>
                            Your application is under review. The Membership Committee will respond within 60 days.
                        </div>
                    @elseif ($membership->status === 'approved' && $membership->isActive())
                        <div class="alert alert-success py-2 mt-3 mb-0 small">
                            <i class="fas fa-check-circle me-1"></i>
                            Your membership is active. You have full access to CCHPL member benefits.
                        </div>
                    @elseif ($membership->status === 'rejected')
                        <div class="alert alert-danger py-2 mt-3 mb-0 small">
                            <i class="fas fa-times-circle me-1"></i>
                            Your application was not approved.
                            @if($membership->rejection_reason)
                                Reason: {{ $membership->rejection_reason }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-credit-card text-muted me-2"></i>Payment History</span>
                    @if($membership->status === 'approved')
                        <a href="{{ route('payment.initiate') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-plus me-1"></i>Pay Now
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if ($membership->payments->isEmpty())
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-receipt fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">No payments yet.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-2">
                            @foreach ($membership->payments as $payment)
                                <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold">M{{ number_format($payment->amount, 2) }}</div>
                                        <div class="small text-muted">
                                            {{ ucfirst($payment->provider) }} · {{ $payment->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="small text-muted font-monospace">{{ $payment->transaction_reference }}</div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="badge {{ $payment->statusBadgeClass() }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        @if ($payment->isVerified())
                                            <a href="{{ route('documents.receipt', $payment->id) }}"
                                               class="btn btn-sm btn-link p-0 text-decoration-none small">
                                                <i class="fas fa-download me-1"></i>Receipt
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- No membership yet -->
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-user-plus text-primary fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Membership Yet</h4>
                    <p class="text-muted mb-4">You haven't applied for membership yet. Join CCHPL to access exclusive professional benefits.</p>
                    <a href="{{ route('membership.apply') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Apply for Membership
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-bolt text-muted me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if (!$membership || $membership->status === 'rejected')
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('membership.apply') }}"
                               class="card text-center border text-decoration-none h-100 p-3 {{ !$membership || $membership->status === 'rejected' ? 'border-success' : '' }}">
                                <div class="text-success mb-2"><i class="fas fa-paper-plane fa-lg"></i></div>
                                <small class="fw-semibold text-dark">Apply for Membership</small>
                            </a>
                        </div>
                    @endif

                    @if ($membership && $membership->status === 'approved')
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('payment.initiate') }}"
                               class="card text-center border text-decoration-none h-100 p-3">
                                <div class="text-primary mb-2"><i class="fas fa-credit-card fa-lg"></i></div>
                                <small class="fw-semibold text-dark">Make a Payment</small>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('documents.certificate', $membership->id) }}"
                               class="card text-center border text-decoration-none h-100 p-3">
                                <div class="text-warning mb-2"><i class="fas fa-certificate fa-lg"></i></div>
                                <small class="fw-semibold text-dark">Download Certificate</small>
                            </a>
                        </div>
                    @endif

                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('member.profile') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-info mb-2"><i class="fas fa-user-edit fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Edit Profile</small>
                        </a>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <form action="{{ route('logout') }}" method="POST" class="h-100">
                            @csrf
                            <button type="submit"
                                    class="card text-center border text-decoration-none h-100 p-3 w-100 bg-white">
                                <div class="text-danger mb-2"><i class="fas fa-sign-out-alt fa-lg"></i></div>
                                <small class="fw-semibold text-dark">Logout</small>
                            </button>
                        </form>
                    </div>

                    @if ($membership && in_array($membership->status, ['approved', 'suspended', 'expired']))
                        <div class="col-sm-6 col-md-3">
                            <a href="{{ route('member.resign.create') }}"
                               class="card text-center border text-decoration-none h-100 p-3">
                                <div class="text-secondary mb-2"><i class="fas fa-door-open fa-lg"></i></div>
                                <small class="fw-semibold text-dark">Submit Resignation</small>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
