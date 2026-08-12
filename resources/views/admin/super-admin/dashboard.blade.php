@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}!</h2>
                    <p class="text-muted mb-0 small">
                        Quick overview of users, membership activity, payments, and administrative tasks.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-label">Registered Users</div>
                    <div class="stat-value">{{ number_format($stats['total_users'] ?? 0) }}</div>
                </div>
            </div>
            <div class="p-3 bg-light rounded">
                <small class="text-muted">Administrator Accounts</small>
                <p class="fw-bold mb-0">{{ number_format($stats['total_admins'] ?? 0) }}</p>
            </div>
            <div class="alert alert-info py-2 mt-3 mb-0 small border-0">
                <i class="fas fa-info-circle me-1"></i>
                Monitor access roles and keep administrative accounts up to date.
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <div class="stat-label">Approved Members</div>
                    <div class="stat-value">{{ number_format($stats['approved_members'] ?? 0) }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                <div>
                    <small class="text-muted">Pending Applications</small>
                    <p class="fw-bold mb-0">{{ number_format($stats['pending_applications'] ?? 0) }}</p>
                </div>
            </div>
            <div class="alert alert-success py-2 mt-3 mb-0 small border-0">
                <i class="fas fa-check-circle me-1"></i>
                Review pending applications promptly to keep member onboarding moving.
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <div class="stat-label">Verified Payments</div>
                    <div class="stat-value">{{ number_format($stats['verified_payments'] ?? 0) }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                <div>
                    <small class="text-muted">Pending Payments</small>
                    <p class="fw-bold mb-0">{{ number_format($stats['pending_payments'] ?? 0) }}</p>
                </div>
            </div>
            <div class="p-3 bg-light rounded mt-3">
                <small class="text-muted">Revenue Collected</small>
                <p class="fw-bold mb-0">M{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="admin-shell-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-file-signature text-muted me-2"></i>Recent Membership Applications
                </h5>
                <a href="{{ route('admin.memberships.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye me-1"></i>Review Queue
                </a>
            </div>
            @if($recentApplications->isEmpty())
                <div class="text-center text-muted py-4">
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

    <div class="col-md-6">
        <div class="admin-shell-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-wallet text-muted me-2"></i>Recent Payments
                </h5>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye me-1"></i>Open Payments
                </a>
            </div>
            @if($recentPayments->isEmpty())
                <div class="text-center text-muted py-4">
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

    {{-- Services Overview --}}
    <div class="col-12">
        <div class="admin-shell-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-list text-muted me-2"></i>Services Overview
                </h5>
                <a href="{{ route('admin.services.index', 'minutes') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Manage Services
                </a>
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-light rounded text-center h-100">
                        <i class="fas fa-file-lines text-brand-green mb-2"></i>
                        <div class="fw-bold fs-5">{{ $stats['total_meeting_minutes'] ?? 0 }}</div>
                        <small class="text-muted">Meeting Minutes</small>
                        <div class="small text-success fw-semibold">{{ $stats['published_meeting_minutes'] ?? 0 }} published</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-light rounded text-center h-100">
                        <i class="fas fa-calendar text-pink mb-2"></i>
                        <div class="fw-bold fs-5">{{ $stats['total_events'] ?? 0 }}</div>
                        <small class="text-muted">Events</small>
                        <div class="small text-success fw-semibold">{{ $stats['published_events'] ?? 0 }} published</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-light rounded text-center h-100">
                        <i class="fas fa-briefcase text-warning mb-2"></i>
                        <div class="fw-bold fs-5">{{ $stats['total_jobs'] ?? 0 }}</div>
                        <small class="text-muted">Job Listings</small>
                        <div class="small text-success fw-semibold">{{ $stats['published_jobs'] ?? 0 }} published</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-light rounded text-center h-100">
                        <i class="fas fa-graduation-cap text-success mb-2"></i>
                        <div class="fw-bold fs-5">{{ $stats['total_scholarships'] ?? 0 }}</div>
                        <small class="text-muted">Scholarships</small>
                        <div class="small text-success fw-semibold">{{ $stats['published_scholarships'] ?? 0 }} published</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-light rounded text-center h-100">
                        <i class="fas fa-handshake text-info mb-2"></i>
                        <div class="fw-bold fs-5">{{ $stats['total_internships'] ?? 0 }}</div>
                        <small class="text-muted">Internships</small>
                        <div class="small text-success fw-semibold">{{ $stats['published_internships'] ?? 0 }} published</div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-2">
                    <div class="p-3 bg-light rounded text-center h-100">
                        <i class="fas fa-chart-pie text-secondary mb-2"></i>
                        <div class="fw-bold fs-5">{{ array_sum(array_slice($stats, 11, 5)) }}</div>
                        <small class="text-muted">Total Published</small>
                        <div class="small text-muted fw-semibold">Across all services</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            <h5 class="fw-semibold mb-3">
                <i class="fas fa-bolt text-muted me-2"></i>Quick Actions
            </h5>
            <div class="row g-3">
                <div class="col-sm-6 col-md-3">
                    <a href="{{ route('admin.admins.list') }}"
                       class="card text-center border text-decoration-none h-100 p-3 stat-card">
                        <div class="stat-icon mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <small class="fw-semibold">Manage Admins</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-3">
                    <a href="{{ route('admin.memberships.index') }}"
                       class="card text-center border text-decoration-none h-100 p-3 stat-card">
                        <div class="stat-icon mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <small class="fw-semibold">Review Memberships</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-3">
                    <a href="{{ route('admin.payments.index') }}"
                       class="card text-center border text-decoration-none h-100 p-3 stat-card">
                        <div class="stat-icon mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <small class="fw-semibold">Verify Payments</small>
                    </a>
                </div>
                <div class="col-sm-6 col-md-3">
                    <a href="{{ route('admin.reports.index') }}"
                       class="card text-center border text-decoration-none h-100 p-3 stat-card">
                        <div class="stat-icon mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <small class="fw-semibold">Open Reports</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
