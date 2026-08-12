@extends('layouts.admin')

@section('content')
<div class="admin-shell-card">
    <div class="mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Financial Report</h1>
            <p class="text-muted mb-0">View and filter payment records.</p>
        </div>
        <a href="{{ route('admin.reports.export.payments', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-filter me-1"></i> Advanced Filters
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.payments') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label fw-semibold small text-muted">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="provider" class="form-label fw-semibold small text-muted">Provider</label>
                        <select name="provider" id="provider" class="form-select">
                            <option value="">All Providers</option>
                            <option value="mpesa" {{ request('provider') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                            <option value="ecocash" {{ request('provider') == 'ecocash' ? 'selected' : '' }}>EcoCash</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold small text-muted">Date From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold small text-muted">Date To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 text-end">
                        <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-secondary me-2">Reset</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between fw-semibold">
            <span>Results ({{ $stats['count'] }})</span>
            <span class="badge bg-success fs-6">Total: M {{ number_format($stats['total_amount'], 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Receipt #</th>
                            <th>Reference</th>
                            <th>Member</th>
                            <th class="text-end">Amount</th>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                        <tr>
                            <td>{{ $p->receipt_number ?? '-' }}</td>
                            <td class="font-monospace small">{{ $p->transaction_reference }}</td>
                            <td>{{ $p->membership?->user?->name ?? 'Unknown' }}</td>
                            <td class="text-end">M {{ number_format($p->amount, 2) }}</td>
                            <td>{{ ucfirst($p->provider) }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($p->status) }}</span></td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No records found matching criteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
