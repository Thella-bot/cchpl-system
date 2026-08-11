@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Resignation Requests</h1>
        <p class="text-muted mb-0">Review and manage member resignation notices.</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.resignations.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.resignations.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.resignations.index', ['status' => 'acknowledged']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'acknowledged' ? 'active' : '' }}">Acknowledged</a>
    </div>
</div>

<div class="admin-shell-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Member</th>
                    <th>Category</th>
                    <th>Submitted</th>
                    <th>Effective Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($resignations as $resignation)
                <tr>
                    <td class="ps-4">
                        <div class="fw-semibold">{{ $resignation->user->name }}</div>
                        <small class="text-muted">{{ $resignation->user->email }}</small>
                    </td>
                    <td>{{ $resignation->membership?->category?->name ?? 'N/A' }}</td>
                    <td>{{ $resignation->created_at->format('d M Y') }}</td>
                    <td>{{ $resignation->effective_date->format('d M Y') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $resignation->reason_code)) }}</td>
                    <td>
                        @if($resignation->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending Review</span>
                        @elseif($resignation->status === 'acknowledged')
                            <span class="badge bg-success">Acknowledged</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($resignation->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.resignations.show', $resignation) }}" class="btn btn-sm btn-primary">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">No resignation requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $resignations->links() }}</div>
</div>
@endsection