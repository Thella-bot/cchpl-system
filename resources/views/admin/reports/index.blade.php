@extends('layouts.admin')

@section('title', 'Reports Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Reports Dashboard</h1>
    <p class="text-muted mb-0">Overview of membership and financial data.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-label">Total Active Members</div>
                    <div class="stat-value">{{ number_format($stats['total_members']) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">M{{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="stat-label">Pending Applications</div>
                    <div class="stat-value">{{ number_format($stats['pending_applications']) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <div class="col-lg-6">
        <div class="admin-shell-card">
            <h5 class="fw-semibold mb-3">
                <i class="fas fa-chart-bar text-muted me-2"></i>Detailed Reports
            </h5>
            <p class="text-muted small">Access detailed, filterable reports and export data to CSV.</p>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.reports.memberships') }}" class="btn btn-primary">
                    <i class="fas fa-user-tag me-2"></i>Membership Report
                </a>
                <a href="{{ route('admin.reports.payments') }}" class="btn btn-primary">
                    <i class="fas fa-money-bill-wave me-2"></i>Financial Report
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-shell-card">
            <h5 class="fw-semibold mb-3">
                <i class="fas fa-chart-pie text-muted me-2"></i>Active Members by Category
            </h5>
            @forelse($membersByCategory as $cat)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold">{{ $cat->name }}</span>
                        <span class="small text-muted">{{ $cat->memberships_count }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $stats['total_members'] > 0 ? ($cat->memberships_count / $stats['total_members']) * 100 : 0 }}%; background: linear-gradient(90deg, var(--brand-primary), var(--brand-light));"
                             aria-valuenow="{{ $cat->memberships_count }}" aria-valuemin="0"
                             aria-valuemax="{{ $stats['total_members'] }}"></div>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">No categories found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
