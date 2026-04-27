@extends('layouts.admin')

@section('title', 'Member Directory')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Member Directory</h1>
    <p class="text-muted mb-0">{{ $members->total() }} Active Members</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($members->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0 fs-5">No active members yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Email</th>
                            <th>Category</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $membership)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $membership->user->name }}</td>
                                <td class="small">{{ $membership->user->email }}</td>
                                <td>{{ $membership->category->name }}</td>
                                <td>
                                    @if($membership->expiry_date)
                                        {{ $membership->expiry_date->format('M d, Y') }}
                                        @if ($membership->isExpired())
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        @elseif ($membership->isExpiringSoon())
                                            <span class="badge bg-warning text-dark ms-1">Expiring Soon</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $membership->statusBadgeClass() }}">
                                        {{ ucfirst($membership->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.memberships.show', $membership->id) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $members->links() }}</div>
        @endif
    </div>
</div>
@endsection
