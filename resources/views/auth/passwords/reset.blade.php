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
                        <i class="fas fa-unlock-keyhole text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Choose a New Password</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Set a new password for your account and return to your member dashboard securely.
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
                        <i class="fas fa-shield text-muted me-2"></i>Password Update
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded mb-3">
                            <small class="text-muted d-block mb-1">Tip</small>
                            <div class="fw-semibold">Use a strong password that you do not reuse on other systems.</div>
                        </div>

                        <div class="alert alert-warning py-2 mt-3 mb-0 small">
                            <i class="fas fa-triangle-exclamation me-1"></i>
                            Make sure your new password is easy for you to remember but difficult for others to guess.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-key text-muted me-2"></i>Reset Details</span>
                        <span class="badge bg-light text-dark border">Protected</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}" class="d-flex flex-column gap-3">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div>
                                <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                                <input id="email" type="email"
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                                       placeholder="you@example.com">
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="form-label fw-semibold small text-muted">New Password</label>
                                <input id="password" type="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="new-password"
                                       placeholder="Create a new password">
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div>
                                <label for="password-confirm" class="form-label fw-semibold small text-muted">Confirm Password</label>
                                <input id="password-confirm" type="password"
                                       class="form-control form-control-lg"
                                       name="password_confirmation" required autocomplete="new-password"
                                       placeholder="Repeat your new password">
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-check me-2"></i>Reset Password
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
