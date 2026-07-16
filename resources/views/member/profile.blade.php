@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row g-4">
    {{-- Page Header --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-brand-green">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-user-edit text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">My Profile</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Keep your member details accurate and maintain secure access to your account.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content (Forms) --}}
    <div class="col-lg-8">
        <div class="vstack gap-4">
            {{-- Personal Information Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-user-pen text-muted me-2"></i>Personal Information</span>
                    <span class="badge bg-light text-dark border">Member Details</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('member.profile.update') }}" class="d-flex flex-column gap-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="form-label fw-semibold small text-muted">Full Name</label>
                            <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg bg-light"
                                   value="{{ $user->email }}" readonly disabled>
                            <div class="form-text text-muted">
                                <i class="fas fa-lock me-1"></i>Email changes require admin support.
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="form-label fw-semibold small text-muted">Phone Number</label>
                            <input id="phone" type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+266 ...">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label for="organization" class="form-label fw-semibold small text-muted">Organisation</label>
                            <input id="organization" type="text" class="form-control form-control-lg @error('organization') is-invalid @enderror"
                                   name="organization" value="{{ old('organization', $user->organization) }}" placeholder="Your workplace or institution">
                            @error('organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-brand-green btn-lg px-4">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-key text-muted me-2"></i>Change Password</span>
                    <span class="badge bg-light text-dark border">Secure Access</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('member.profile.password') }}" class="d-flex flex-column gap-3">
                        @csrf
                        @method('PUT')

                        <div class="password-field">
                            <label for="current_password" class="form-label fw-semibold small text-muted">Current Password</label>
                            <input id="current_password" type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                                   name="current_password" required autocomplete="current-password">
                            <button type="button" class="password-toggle-btn" data-target="current_password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="password-field">
                            <label for="password" class="form-label fw-semibold small text-muted">New Password</label>
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" data-target="password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            <div class="form-text">Use at least 8 characters with upper and lowercase letters plus numbers.</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="password-field">
                            <label for="password_confirmation" class="form-label fw-semibold small text-muted">Confirm New Password</label>
                            <input id="password_confirmation" type="password" class="form-control form-control-lg"
                                   name="password_confirmation" required autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" data-target="password_confirmation" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-brand-green btn-lg px-4">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar (Info & Actions) --}}
    <div class="col-lg-4">
        <div class="vstack gap-4">
            {{-- Profile Snapshot Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-address-card text-muted me-2"></i>Profile Snapshot
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded mb-3">
                        <small class="text-muted d-block mb-1">Member Name</small>
                        <div class="fw-bold fs-5">{{ $user->name }}</div>
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Email</dt>
                        <dd class="col-7">{{ $user->email }}</dd>

                        <dt class="col-5 text-muted">Phone</dt>
                        <dd class="col-7">{{ $user->phone ?: 'Not provided' }}</dd>

                        <dt class="col-5 text-muted">Organisation</dt>
                        <dd class="col-7">{{ $user->organization ?: 'Not provided' }}</dd>
                    </dl>

                    <div class="alert alert-info py-2 mt-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Your email is locked for account protection.
                    </div>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-bolt text-muted me-2"></i>Quick Actions
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('member.dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-house text-brand-green fa-fw me-3"></i>
                            <span class="fw-semibold">Return to Dashboard</span>
                        </a>
                        <a href="{{ route('membership.apply') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-paper-plane text-brand-green fa-fw me-3"></i>
                            <span class="fw-semibold">Membership Application</span>
                        </a>
                        <a href="{{ route('payment.initiate') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-credit-card text-brand-green fa-fw me-3"></i>
                            <span class="fw-semibold">View Payments</span>
                        </a>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-sign-out-alt text-danger fa-fw me-3"></i>
                            <span class="fw-semibold text-danger">Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection