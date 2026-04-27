@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-gray-800">Resignation Requests</h2>
        <div class="btn-group">
            <a href="{{ route('admin.resignations.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.resignations.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('admin.resignations.index', ['status' => 'acknowledged']) }}" class="btn btn-sm btn-outline-secondary {{ request('status') == 'acknowledged' ? 'active' : '' }}">Acknowledged</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Member</th>
                            <th>Category</th>
                            <th>Submitted</th>
                            <th>Effective Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resignations as $resignation)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $resignation->user->name }}</div>
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
                            <td colspan="7" class="text-center py-4 text-muted">No resignation requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($resignations->hasPages())
            <div class="card-footer">
                {{ $resignations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection