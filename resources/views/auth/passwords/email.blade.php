@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-key text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Reset Your Password</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Enter your email address and we will send you a secure reset link.
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
                        <i class="fas fa-lock text-muted me-2"></i>Account Recovery
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded mb-3">
                            <small class="text-muted d-block mb-1">Need access again?</small>
                            <div class="fw-semibold">We’ll email you a password reset link for your account.</div>
                        </div>

                        <div class="alert alert-info py-2 mt-3 mb-0 small">
                            <i class="fas fa-circle-info me-1"></i>
                            Use the same email address you registered with in the system.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-envelope text-muted me-2"></i>Send Reset Link</span>
                        <span class="badge bg-light text-dark border">Secure Recovery</span>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="d-flex flex-column gap-3">
                            @csrf

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

                            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Send Password Reset Link
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Login
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
