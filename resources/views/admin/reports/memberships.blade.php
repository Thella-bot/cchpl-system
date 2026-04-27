@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Membership Report</h1>
        <a href="{{ route('admin.reports.export.members', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-1"></i> Advanced Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.memberships') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Joined After</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Joined Before</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.reports.memberships') }}" class="btn btn-outline-secondary me-2">Reset</a>
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
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Member ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memberships as $m)
                        <tr>
                            <td>{{ $m->member_id ?? 'N/A' }}</td>
                            <td>
                                <div class="fw-bold">{{ $m->user->name }}</div>
                                <small class="text-muted">{{ $m->user->email }}</small>
                            </td>
                            <td>{{ $m->category->name }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($m->status) }}</span></td>
                            <td>{{ $m->created_at->format('d M Y') }}</td>
                            <td>{{ $m->expiry_date ? $m->expiry_date->format('d M Y') : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4">No records found matching criteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">{{ $memberships->links() }}</div>
    </div>
</div>
@endsection