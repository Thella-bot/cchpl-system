@extends('layouts.admin')

@section('title', 'All Members')

@section('content')
@php($canEditMemberEmail = auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))

<div class="mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">All Members</h1>
        <p class="text-muted mb-0">View approved members and manage their membership status.</p>
    </div>
    <form class="d-flex align-items-center gap-2" method="GET" action="">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email..."
               class="form-control form-control-sm" style="min-width: 200px;">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

@if($expiringCount > 0 || $expiredCount > 0)
    <div class="row g-3 mb-4">
        @if($expiringCount > 0)
            <div class="col-sm-6">
                <div class="alert alert-warning py-2 mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>{{ $expiringCount }}</strong> member(s) expiring within 30 days.
                </div>
            </div>
        @endif
        @if($expiredCount > 0)
            <div class="col-sm-6">
                <div class="alert alert-danger py-2 mb-0">
                    <i class="fas fa-times-circle me-1"></i>
                    <strong>{{ $expiredCount }}</strong> member(s) have expired memberships.
                </div>
            </div>
        @endif
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($members->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0 fs-5">No members found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Member</th>
                            @if($canEditMemberEmail)
                                <th style="min-width: 280px;">Email</th>
                            @endif
                            <th>Category</th>
                            <th>Annual Fee</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $member->user->name }}</div>
                                    @if(!$canEditMemberEmail)
                                        <div class="small text-muted">{{ $member->user->email }}</div>
                                    @endif
                                    @if($member->member_id)
                                        <div class="small font-monospace text-muted">{{ $member->member_id }}</div>
                                    @endif
                                </td>
                                @if($canEditMemberEmail)
                                    <td>
                                        <form method="POST" action="{{ route('admin.memberships.email.update', $member->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="input-group input-group-sm">
                                                <input
                                                    type="email"
                                                    name="email"
                                                    value="{{ old('email', $member->user->email) }}"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    required
                                                    maxlength="255"
                                                    aria-label="Email address for {{ $member->user->name }}">
                                                <button type="submit" class="btn btn-outline-primary" title="Save email">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </form>
                                    </td>
                                @endif
                                <td>{{ $member->category->name }}</td>
                                <td>M{{ number_format($member->category->annual_fee, 2) }}</td>
                                <td>
                                    @if($member->expiry_date)
                                        <span class="{{ $member->isExpired() ? 'text-danger fw-bold' : ($member->isExpiringSoon() ? 'text-warning fw-bold' : '') }}">
                                            {{ $member->expiry_date->format('M d, Y') }}
                                        </span>
                                        @if($member->isExpired())
                                            <span class="badge bg-danger ms-1">Expired</span>
                                        @elseif($member->isExpiringSoon())
                                            <span class="badge bg-warning text-dark ms-1">Soon</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $member->statusBadgeClass() }}">{{ ucfirst($member->status) }}</span>
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
