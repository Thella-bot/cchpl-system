@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo/cchpl-alt-logo.png') }}" alt="CCHPL Logo" height="60" class="mb-3">
                    <h2 class="fw-bold mb-1">Create Your Account</h2>
                    <p class="text-muted">Join the CCHPL community by creating your member account.</p>
                </div>

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

                    <div class="password-field">
                        <label for="password" class="form-label fw-semibold small text-muted">Password</label>
                        <input id="password" type="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password"
                               placeholder="Create a secure password">
                        <button type="button" class="password-toggle-btn" data-target="password" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                            <i class="fas fa-eye-slash"></i>
                        </button>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="password-field">
                        <label for="password-confirm" class="form-label fw-semibold small text-muted">Confirm Password</label>
                        <input id="password-confirm" type="password"
                               class="form-control form-control-lg"
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="Repeat your password">
                        <button type="button" class="password-toggle-btn" data-target="password-confirm" aria-label="Show password">
                            <i class="fas fa-eye"></i>
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>

                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-brand-green btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light text-center p-3">
                <span class="small text-muted">Already have an account?</span>
                <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Login here</a>
            </div>
        </div>
    </div>
</div>
@endsection