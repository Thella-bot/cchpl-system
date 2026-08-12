@extends('layouts.admin')

@section('content')
<div class="admin-shell-card">
    <div class="mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Membership Report</h1>
            <p class="text-muted mb-0">View and filter membership records.</p>
        </div>
        <a href="{{ route('admin.reports.export.members', request()->all()) }}" class="btn btn-primary">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-filter me-1"></i> Advanced Filters
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.memberships') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label fw-semibold small text-muted">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="category_id" class="form-label fw-semibold small text-muted">Category</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold small text-muted">Joined After</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold small text-muted">Joined Before</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 text-end">
                        <a href="{{ route('admin.reports.memberships') }}" class="btn btn-outline-secondary me-2">Reset</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Results ({{ $stats['count'] }})</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
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
                                <div class="fw-semibold">{{ $m->user->name }}</div>
                                <small class="text-muted">{{ $m->user->email }}</small>
                            </td>
                            <td>{{ $m->category->name }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($m->status) }}</span></td>
                            <td>{{ $m->created_at->format('d M Y') }}</td>
                            <td>{{ $m->expiry_date ? $m->expiry_date->format('d M Y') : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No records found matching criteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">{{ $memberships->links() }}</div>
    </div>
</div>
@endsection
