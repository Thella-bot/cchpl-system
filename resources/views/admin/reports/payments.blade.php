@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financial Report</h1>
        <a href="{{ route('admin.reports.export.payments', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-1"></i> Advanced Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.payments') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Provider</label>
                        <select name="provider" class="form-select">
                            <option value="">All Providers</option>
                            <option value="mpesa" {{ request('provider') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                            <option value="ecocash" {{ request('provider') == 'ecocash' ? 'selected' : '' }}>EcoCash</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Date From</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Date To</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-secondary me-2">Reset</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">Results ({{ $stats['count'] }})</h6>
            <span class="badge bg-success fs-6">Total: M {{ number_format($stats['total_amount'], 2) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Receipt #</th>
                            <th>Reference</th>
                            <th>Member</th>
                            <th>Amount</th>
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
                            <td>M {{ number_format($p->amount, 2) }}</td>
                            <td>{{ ucfirst($p->provider) }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($p->status) }}</span></td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4">No records found matching criteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $payments->links() }}</div>
    </div>
</div>
@endsection