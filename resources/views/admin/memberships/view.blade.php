@extends('layouts.admin')

@section('title', 'View Membership')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.memberships.list') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i>Back to Members
    </a>
    <h1 class="h3 fw-bold mt-2 mb-0">Membership Details</h1>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-user text-muted me-2"></i>Member Information
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7 fw-semibold">{{ $membership->user->name }}</dd>
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $membership->user->email }}</dd>
                    <dt class="col-5 text-muted">Phone</dt>
                    <dd class="col-7">{{ $membership->user->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Organisation</dt>
                    <dd class="col-7">{{ $membership->user->organization ?? '—' }}</dd>
                </dl>
                @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
                    <form method="POST" action="{{ route('admin.memberships.email.update', $membership->id) }}" class="mt-3 pt-3 border-top">
                        @csrf
                        @method('PUT')
                        <label for="member-email-{{ $membership->id }}" class="form-label small fw-semibold text-muted">
                            Update Email Address
                        </label>
                        <div class="input-group input-group-sm">
                            <input
                                type="email"
                                id="member-email-{{ $membership->id }}"
                                name="email"
                                value="{{ old('email', $membership->user->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                                maxlength="255">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-save me-1"></i>Save
                            </button>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text small">Changing the address will require the member to verify the new email.</div>
                    </form>
                @endif
            </div>
        </div>
    </div>

<div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-id-card text-muted me-2"></i>Membership Details
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Category</dt>
                    <dd class="col-7 fw-semibold">{{ $membership->category->name }}</dd>
                    <dt class="col-5 text-muted">Annual Fee</dt>
                    <dd class="col-7 fw-semibold">M{{ number_format($membership->category->annual_fee, 2) }}</dd>
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge {{ $membership->statusBadgeClass() }}">{{ ucfirst($membership->status) }}</span>
                    </dd>
                    <dt class="col-5 text-muted">Member ID</dt>
                    <dd class="col-7 font-monospace">{{ $membership->member_id ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Expiry Date</dt>
                    <dd class="col-7">{{ $membership->expiry_date?->format('M d, Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Applied</dt>
                    <dd class="col-7">{{ $membership->created_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@if($membership->documents->isNotEmpty())
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-folder-open text-muted me-2"></i>Documents
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($membership->documents as $doc)
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                       class="badge bg-light text-dark border text-decoration-none p-2">
                        <i class="fas fa-file me-1"></i>{{ $doc->document_type }}: {{ $doc->original_name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
@endsection
