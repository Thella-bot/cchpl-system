@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
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

    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
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
                    Your email address is locked here for account protection and verification integrity.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
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
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-key text-muted me-2"></i>Change Password</span>
                <span class="badge bg-light text-dark border">Secure Access</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('member.profile.password') }}" class="d-flex flex-column gap-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="form-label fw-semibold small text-muted">Current Password</label>
                        <input id="current_password" type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                               name="current_password" required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="password" class="form-label fw-semibold small text-muted">New Password</label>
                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password">
                        <div class="form-text">Use at least 8 characters with upper and lowercase letters plus numbers.</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label fw-semibold small text-muted">Confirm New Password</label>
                        <input id="password_confirmation" type="password" class="form-control form-control-lg"
                               name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-key me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-bolt text-muted me-2"></i>Quick Actions
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('member.dashboard') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-success mb-2"><i class="fas fa-house fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Dashboard</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('membership.apply') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-primary mb-2"><i class="fas fa-paper-plane fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Apply</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('payment.initiate') }}"
                           class="card text-center border text-decoration-none h-100 p-3">
                            <div class="text-warning mb-2"><i class="fas fa-credit-card fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Payments</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <form action="{{ route('logout') }}" method="POST" class="h-100">
                            @csrf
                            <button type="submit" class="card text-center border text-decoration-none h-100 p-3 w-100 bg-white">
                                <div class="text-danger mb-2"><i class="fas fa-sign-out-alt fa-lg"></i></div>
                                <small class="fw-semibold text-dark">Logout</small>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
