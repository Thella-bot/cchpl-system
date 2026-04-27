@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-envelope-circle-check text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Verify Your Email</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Confirm your email address to unlock protected member actions and complete your setup.
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
                        <i class="fas fa-envelope-open-text text-muted me-2"></i>Check Your Inbox
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded mb-3">
                            <small class="text-muted d-block mb-1">Next step</small>
                            <div class="fw-semibold">Open the verification email we sent and click the secure confirmation link.</div>
                        </div>

                        <div class="alert alert-info py-2 mt-3 mb-0 small">
                            <i class="fas fa-circle-info me-1"></i>
                            If the message is not in your inbox, also check your spam or promotions folder.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-badge-check text-muted me-2"></i>Verification Status</span>
                        <span class="badge bg-light text-dark border">Email Required</span>
                    </div>
                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success">
                                A fresh verification link has been sent to your email address.
                            </div>
                        @endif

                        <p class="text-muted mb-3">
                            Before proceeding, please check your email for a verification link.
                        </p>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <form method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Resend Verification Email
                                </button>
                            </form>

                            <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-home me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
