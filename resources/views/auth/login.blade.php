@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-right-to-bracket text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Welcome Back</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Sign in to access the Council for Culinary and Hospitality Professionals Lesotho portal.
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
                        <i class="fas fa-shield-heart text-muted me-2"></i>Member Access
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded mb-3">
                            <small class="text-muted d-block mb-1">Inside the portal you can</small>
                            <div class="fw-semibold">Manage membership, payments, documents, and your profile.</div>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px;">
                                    <i class="fas fa-id-card text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Track membership status</div>
                                    <div class="text-muted small">See approvals, renewals, and expiry details.</div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px;">
                                    <i class="fas fa-credit-card text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Manage payments</div>
                                    <div class="text-muted small">Submit proof of payment and download receipts.</div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px;">
                                    <i class="fas fa-file-arrow-down text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Access documents</div>
                                    <div class="text-muted small">Download certificates and member documents when available.</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success py-2 mt-3 mb-0 small">
                            <i class="fas fa-circle-info me-1"></i>
                            New to the system? Use the registration page to create your member account first.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-user-lock text-muted me-2"></i>Login Details</span>
                        <span class="badge bg-light text-dark border">Secure Access</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}" class="d-flex flex-column gap-3">
                            @csrf

                            <div>
                                <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                                <input id="email" type="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                       placeholder="you@example.com">
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="form-label fw-semibold small text-muted">Password</label>
                                <input id="password" type="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="current-password"
                                       placeholder="Enter your password">
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="remember">
                                        Remember Me
                                    </label>
                                </div>

                                @if (Route::has('password.request'))
                                    <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                        Forgot Your Password?
                                    </a>
                                @endif
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-right-to-bracket me-2"></i>Login
                                </button>
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
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
