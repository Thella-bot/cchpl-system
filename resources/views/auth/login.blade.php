@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" height="60" class="mb-3">
                    <h2 class="fw-bold mb-1">Member Portal Login</h2>
                    <p class="text-muted">Sign in to access your CCHPL account.</p>
                </div>

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

                    <div class="password-field">
                        <label for="password" class="form-label fw-semibold small text-muted">Password</label>
                        <div class="position-relative">
                            <input id="password" type="password"
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password"
                                   placeholder="Enter your password">
                            <button type="button" class="password-toggle-btn" data-target="password" aria-label="Show password">
                                <i class="fas fa-eye"></i>
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
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

                    <div class="d-grid pt-2">
                        <button type="submit" class="btn btn-brand-green btn-lg">
                            <i class="fas fa-right-to-bracket me-2"></i>Secure Login
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light text-center p-3">
                <span class="small text-muted">Don't have an account?</span>
                <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Create one here</a>
            </div>
        </div>
    </div>
</div>
@endsection