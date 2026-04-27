@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-user-shield text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Welcome, {{ auth()->user()->name }}!</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Quick overview of users, membership activity, payments, and administrative tasks.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-users text-muted me-2"></i>User Overview
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-5 fw-bold">Registered Users</span>
                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-2 fs-6">
                        {{ number_format($stats['total_users'] ?? 0) }}
                    </span>
                </div>

                <div class="p-3 bg-light rounded mb-3">
                    <small class="text-muted">Administrator Accounts</small>
                    <p class="fw-bold mb-0 fs-5">{{ number_format($stats['total_admins'] ?? 0) }}</p>
                </div>

                <div class="alert alert-info py-2 mt-3 mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Monitor access roles and keep administrative accounts up to date.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-id-card text-muted me-2"></i>Membership Status
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-6 fw-semibold">Approved Members</span>
                    <span class="badge bg-success px-3 py-2 fs-6">
                        {{ number_format($stats['approved_members'] ?? 0) }}
                    </span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-6 fw-semibold">Pending Applications</span>
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                        {{ number_format($stats['pending_applications'] ?? 0) }}
                    </span>
                </div>

                <div class="alert alert-success py-2 mt-3 mb-0 small">
                    <i class="fas fa-check-circle me-1"></i>
                    Review pending applications promptly to keep member onboarding moving.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-credit-card text-muted me-2"></i>Payment Snapshot
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-6 fw-semibold">Verified Payments</span>
                    <span class="badge bg-success px-3 py-2 fs-6">
                        {{ number_format($stats['verified_payments'] ?? 0) }}
                    </span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-6 fw-semibold">Pending Payments</span>
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                        {{ number_format($stats['pending_payments'] ?? 0) }}
                    </span>
                </div>

                <div class="p-3 bg-light rounded">
                    <small class="text-muted">Revenue Collected</small>
                    <p class="fw-bold mb-0 fs-5">M{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-file-signature text-muted me-2"></i>Recent Membership Applications</span>
                <a href="{{ route('admin.memberships.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-eye me-1"></i>Review Queue
                </a>
            </div>
            <div class="card-body">
                @if($recentApplications->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                        <p class="small mb-0">No recent applications.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($recentApplications as $app)
                            <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold">{{ $app->user->name }}</div>
                                    <div class="small text-muted">
                                        {{ $app->user->email }} · {{ $app->category->name ?? 'Unassigned' }}
                                    </div>
                                    <div class="small text-muted">{{ $app->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="badge {{ $app->status === 'approved' ? 'bg-success' : ($app->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-wallet text-muted me-2"></i>Recent Payments</span>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-eye me-1"></i>Open Payments
                </a>
            </div>
            <div class="card-body">
                @if($recentPayments->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-receipt fa-2x mb-2 opacity-50"></i>
                        <p class="small mb-0">No recent payments.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($recentPayments as $payment)
                            <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold">M{{ number_format($payment->amount, 2) }}</div>
                                    <div class="small text-muted">
                                        {{ $payment->membership?->user?->name ?? 'Unknown' }} · {{ $payment->membership?->category?->name ?? 'No category' }}
                                    </div>
                                    <div class="small text-muted">{{ $payment->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="badge {{ $payment->status === 'verified' ? 'bg-success' : ($payment->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-bolt text-muted me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('admin.admins.list') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-success mb-2"><i class="fas fa-users-cog fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Manage Admins</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('admin.memberships.index') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-primary mb-2"><i class="fas fa-id-card fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Review Memberships</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('admin.payments.index') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-warning mb-2"><i class="fas fa-credit-card fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Verify Payments</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="{{ route('admin.reports.index') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-info mb-2"><i class="fas fa-chart-line fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Open Reports</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
