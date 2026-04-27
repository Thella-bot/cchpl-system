@extends('layouts.admin')

@section('title', 'Rejected Applications')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Rejected Applications</h1>
        <p class="text-muted mb-0">Applications that were not approved by the Membership Committee.</p>
    </div>
    <a href="{{ route('admin.memberships.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Pending
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($rejected->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0 fs-5">No rejected applications found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Applicant</th>
                            <th>Category</th>
                            <th>Fee</th>
                            <th>Rejected On</th>
                            <th>Rejection Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rejected as $membership)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $membership->user->name }}</div>
                                    <div class="small text-muted">{{ $membership->user->email }}</div>
                                </td>
                                <td>{{ $membership->category->name }}</td>
                                <td>M{{ number_format($membership->category->annual_fee, 2) }}</td>
                                <td class="small text-muted">{{ $membership->updated_at->format('M d, Y') }}</td>
                                <td class="small text-muted">{{ $membership->rejection_reason ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $rejected->links() }}</div>
        @endif
    </div>
</div>
@endsection
