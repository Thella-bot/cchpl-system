@extends('layouts.admin')
@section('title', 'Membership Report')

@section('content')
<div class="admin-shell-card">
    <div class="mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-decoration-none text-muted">&larr; Reports dashboard</a>
            <h1 class="h3 fw-bold mb-1">Membership Report</h1>
            <p class="text-muted mb-0">Generated: {{ now()->format('d F Y, H:i') }}</p>
        </div>
        <a href="{{ route('admin.reports.export.members') }}" class="btn btn-primary">
            Export CSV
        </a>
    </div>

    {{-- By category --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">Membership by category</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Active</th>
                            <th class="text-end">Pending</th>
                            <th class="text-end">Suspended</th>
                            <th class="text-end">Expired</th>
                            <th class="text-end">Revenue (M)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $cat)
                        <tr>
                            <td class="fw-semibold">{{ $cat['name'] }}</td>
                            <td class="text-end">{{ number_format($cat['total']) }}</td>
                            <td class="text-end text-success fw-semibold">{{ number_format($cat['approved']) }}</td>
                            <td class="text-end text-warning">{{ number_format($cat['pending']) }}</td>
                            <td class="text-end text-danger">{{ number_format($cat['suspended']) }}</td>
                            <td class="text-end text-orange">{{ number_format($cat['expired']) }}</td>
                            <td class="text-end fw-semibold">M{{ number_format($cat['revenue'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Status summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f97316, #ea580c); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="stat-label">Expired memberships</div>
                        <div class="stat-value">{{ number_format($expiredCount) }}</div>
                        <small class="text-muted">Marked by scheduled command</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <div class="stat-label">Suspended (non-payment 6+ mo.)</div>
                        <div class="stat-value">{{ number_format($suspendedCount) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #6b7280, #4b5563); box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div>
                        <div class="stat-label">Resigned</div>
                        <div class="stat-value">{{ number_format($resignedCount) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Expiring within 30 days --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between fw-semibold">
            <span>Expiring within 30 days</span>
            <span class="badge bg-warning text-dark">{{ $expiringMembers->count() }} member(s)</span>
        </div>
        @if ($expiringMembers->isEmpty())
            <div class="text-center py-5 text-muted">No memberships expiring in the next 30 days.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member</th>
                            <th>Member ID</th>
                            <th>Category</th>
                            <th>Expires</th>
                            <th class="text-end">Days left</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expiringMembers as $m)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $m->user->name }}</div>
                                <small class="text-muted">{{ $m->user->email }}</small>
                            </td>
                            <td class="font-monospace small">{{ $m->member_id ?? '—' }}</td>
                            <td>{{ $m->category->name }}</td>
                            <td>{{ $m->expiry_date->format('d M Y') }}</td>
                            <td class="text-end">
                                @php $days = $m->daysUntilExpiry(); @endphp
                                <span class="fw-semibold {{ $days <= 7 ? 'text-danger' : 'text-warning' }}">{{ $days }} day(s)</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
