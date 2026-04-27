@extends('layouts.admin')

@section('title', 'Reports Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Reports Dashboard</h1>
    <p class="text-muted mb-0">Overview of membership and financial data.</p>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-users text-primary fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($stats['total_members']) }}</div>
                    <div class="small text-muted">Total Active Members</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-coins text-success fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">M{{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="small text-muted">Total Revenue</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-clipboard-list text-warning fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($stats['pending_applications']) }}</div>
                    <div class="small text-muted">Pending Applications</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Detailed Reports Links -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-bar text-muted me-2"></i>Detailed Reports
            </div>
            <div class="card-body">
                <p class="text-muted small">Access detailed, filterable reports and export data to CSV.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.reports.memberships') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-tag me-2"></i>Membership Report
                    </a>
                    <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-success">
                        <i class="fas fa-money-bill-wave me-2"></i>Financial Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-pie text-muted me-2"></i>Active Members by Category
            </div>
            <div class="card-body">
                @forelse($membersByCategory as $cat)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold">{{ $cat->name }}</span>
                            <span class="small text-muted">{{ $cat->memberships_count }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                 style="width: {{ $stats['total_members'] > 0 ? ($cat->memberships_count / $stats['total_members']) * 100 : 0 }}%"
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
</div>
@endsection
