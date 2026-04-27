@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-user-plus text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Create Your Account</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Register once to begin your CCHPL membership journey and manage everything from one place.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-10">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold">
                        <i class="fas fa-seedling text-muted me-2"></i>Getting Started
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded mb-3">
                            <small class="text-muted d-block mb-1">After registration you can</small>
                            <div class="fw-semibold">Apply for membership, track payments, and download your documents.</div>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px;">
                                    <i class="fas fa-paper-plane text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Submit your application</div>
                                    <div class="text-muted small">Start the membership process from your personal dashboard.</div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px;">
                                    <i class="fas fa-envelope-open-text text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Verify your email</div>
                                    <div class="text-muted small">Verification unlocks the protected member actions.</div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px;">
                                    <i class="fas fa-user-shield text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Manage your account</div>
                                    <div class="text-muted small">Keep your profile details and future renewals in one place.</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 mt-3 mb-0 small">
                            <i class="fas fa-circle-info me-1"></i>
                            Already registered? Use the login page to access your dashboard.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-id-badge text-muted me-2"></i>Registration Details</span>
                        <span class="badge bg-light text-dark border">Member Signup</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}" class="d-flex flex-column gap-3">
                            @csrf

                            <div>
                                <label for="name" class="form-label fw-semibold small text-muted">Full Name</label>
                                <input id="name" type="text"
                                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                       placeholder="Enter your full name">
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                                <input id="email" type="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email"
                                       placeholder="you@example.com">
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="form-label fw-semibold small text-muted">Password</label>
                                <input id="password" type="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="new-password"
                                       placeholder="Create a password">
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div>
                                <label for="password-confirm" class="form-label fw-semibold small text-muted">Confirm Password</label>
                                <input id="password-confirm" type="password"
                                       class="form-control form-control-lg"
                                       name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Repeat your password">
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-user-plus me-2"></i>Register
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-right-to-bracket me-2"></i>Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
