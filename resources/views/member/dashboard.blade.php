@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="row g-4">

    {{-- Welcome Header --}}
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 72px; height: 72px;">
                    <i class="fas fa-user text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Welcome, {{ Auth::user()->name }}!</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Manage your membership, payments, and profile from one place.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($membership)
        {{-- Membership Status Card --}}
        <div class="col-12 col-lg-6">
            <div class="admin-shell-card h-100 animate-fade-in" style="animation-delay: 0.1s;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="stat-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h5 class="fw-semibold mb-0">Membership Status</h5>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fs-5 fw-bold">{{ $membership->category->name }}</span>
                    <span class="badge px-3 py-2 {{ $membership->statusBadgeClass() }} fs-6">
                        {{ ucfirst($membership->status) }}
                    </span>
                </div>

                @if ($membership->member_id)
                    <div class="p-3 bg-light rounded mb-3">
                        <small class="text-muted">Member ID</small>
                        <p class="fw-bold font-monospace mb-0 fs-5">{{ $membership->member_id }}</p>
                    </div>
                @endif

                <div class="row g-3 small mb-0">
                    <div class="col-6">
                        <span class="text-muted">Annual Fee</span>
                        <p class="fw-semibold mb-0">M{{ number_format($membership->category->annual_fee, 2) }}</p>
                    </div>
                    @if ($membership->expiry_date)
                        <div class="col-6">
                            <span class="text-muted">Expires</span>
                            <p class="fw-semibold mb-0 {{ $membership->isExpiringSoon() ? 'text-warning' : '' }} {{ $membership->isExpired() ? 'text-danger' : '' }}">
                                {{ $membership->expiry_date->format('d F Y') }}
                            </p>
                            @if ($membership->isExpiringSoon() && !$membership->isExpired())
                                <span class="badge bg-warning text-dark">Expiring soon</span>
                            @endif
                            @if ($membership->isExpired())
                                <span class="badge bg-danger">Expired</span>
                            @endif
                        </div>
                    @endif
                    <div class="col-6">
                        <span class="text-muted">Applied</span>
                        <p class="mb-0">{{ $membership->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="mt-3">
                    @if ($membership->status === 'pending')
                        <div class="alert alert-info py-2 mb-0 small border-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Your application is under review. You'll be notified of the outcome.
                        </div>
                    @elseif ($membership->status === 'approved' && $membership->isActive())
                        <div class="alert alert-success py-2 mb-0 small border-0">
                            <i class="fas fa-check-circle me-1"></i>
                            Your membership is active and in good standing.
                        </div>
                    @elseif ($membership->status === 'rejected')
                        <div class="alert alert-danger py-2 mb-0 small border-0">
                            <i class="fas fa-times-circle me-1"></i>
                            Your application was not approved.
                            @if($membership->rejection_reason)
                                <strong>Reason:</strong> {{ $membership->rejection_reason }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="col-12 col-lg-6">
            <div class="admin-shell-card h-100 animate-fade-in" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Payment History</h5>
                    </div>
                    @if($membership->status === 'approved')
                        <a href="{{ route('payment.initiate') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>New Payment
                        </a>
                    @endif
                </div>

                @if ($membership->payments->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-receipt fa-2x mb-2 opacity-50"></i>
                        <p class="small mb-0">No payments recorded yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Amount</th>
                                    <th>Provider</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($membership->payments->take(5) as $payment)
                                    <tr>
                                        <td class="fw-semibold">M{{ number_format($payment->amount, 2) }}</td>
                                        <td class="small">{{ ucfirst($payment->provider) }}</td>
                                        <td class="small text-muted">{{ $payment->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $payment->statusBadgeClass() }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($membership->payments->count() > 5)
                        <div class="text-center mt-3">
                            <small class="text-muted">Showing last 5 of {{ $membership->payments->count() }} payments</small>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @else
        {{-- No Membership Yet --}}
        <div class="col-12">
            <div class="stat-card text-center py-5 animate-fade-in" style="background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border: 2px dashed #e2e8f0;">
                <div class="d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width: 80px; height: 80px; border-radius: 50%; background: rgba(26, 107, 60, 0.1);">
                    <i class="fas fa-user-plus fs-2" style="color: var(--brand-primary);"></i>
                </div>
                <h4 class="fw-bold mb-2">Begin Your Membership Journey</h4>
                <p class="text-muted mb-4">You haven't applied for membership yet. Join CCHPL to access exclusive professional benefits.</p>
                <a href="{{ route('membership.apply') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Apply for Membership
                </a>
            </div>
        </div>
    @endif

    @if ($membership && $membership->status === 'approved')
        {{-- Services Overview --}}
        <div class="col-12">
            <div class="admin-shell-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <h5 class="fw-semibold mb-0">Available Services</h5>
                    </div>
                    <span class="badge bg-success">Your Membership: {{ $membership->category->name }}</span>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('member.services.minutes') }}" class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <i class="fas fa-file-lines text-primary mb-2"></i>
                            <div class="fw-bold fs-5">{{ $serviceStats['meeting_minutes'] }}</div>
                            <small class="text-muted">Meeting Minutes</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('member.services.events') }}" class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <i class="fas fa-calendar-star text-pink mb-2"></i>
                            <div class="fw-bold fs-5">{{ $serviceStats['events'] }}</div>
                            <small class="text-muted">Events</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('member.services.jobs') }}" class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <i class="fas fa-briefcase text-warning mb-2"></i>
                            <div class="fw-bold fs-5">{{ $serviceStats['jobs'] }}</div>
                            <small class="text-muted">Job Listings</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('member.services.scholarships') }}" class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <i class="fas fa-graduation-cap text-success mb-2"></i>
                            <div class="fw-bold fs-5">{{ $serviceStats['scholarships'] }}</div>
                            <small class="text-muted">Scholarships</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('member.services.internships') }}" class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <i class="fas fa-handshake text-info mb-2"></i>
                            <div class="fw-bold fs-5">{{ $serviceStats['internships'] }}</div>
                            <small class="text-muted">Internships</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-2">
                        <a href="{{ route('member.services.minutes') }}" class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <i class="fas fa-arrow-right text-secondary mb-2"></i>
                            <div class="fw-bold fs-5">{{ array_sum($serviceStats) }}</div>
                            <small class="text-muted">Total Available</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Actions --}}
    <div class="col-12">
        <div class="admin-shell-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
                    <i class="fas fa-bolt"></i>
                </div>
                <h5 class="fw-semibold mb-0">Quick Actions</h5>
            </div>
            <div class="row g-3">
                @if (!$membership || $membership->status === 'rejected')
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('membership.apply') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <small class="fw-semibold">Apply for Membership</small>
                        </a>
                    </div>
                @endif

                @if ($membership && $membership->status === 'approved')
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('payment.initiate') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <small class="fw-semibold">Make a Payment</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('documents.certificate', $membership->id) }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <small class="fw-semibold">Download Certificate</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('member.services.minutes') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                                <i class="fas fa-file-lines"></i>
                            </div>
                            <small class="fw-semibold">Meeting Minutes</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('member.services.events') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #ec4899, #db2777); box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
                                <i class="fas fa-calendar-star"></i>
                            </div>
                            <small class="fw-semibold">Events</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('member.services.jobs') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <small class="fw-semibold">Job Listings</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('member.services.scholarships') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <small class="fw-semibold">Scholarships</small>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('member.services.internships') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #06b6d4, #0891b2); box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <small class="fw-semibold">Internships</small>
                        </a>
                    </div>
                @endif

                <div class="col-sm-6 col-md-4">
                    <a href="{{ route('member.profile') }}"
                       class="card text-center border text-decoration-none h-100 p-3 stat-card">
                        <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <small class="fw-semibold">Edit Your Profile</small>
                    </a>
                </div>

                <div class="col-sm-6 col-md-4">
                    <form action="{{ route('logout') }}" method="POST" class="h-100">
                        @csrf
                        <button type="submit"
                                class="card text-center border text-decoration-none h-100 p-3 w-100 bg-white">
                            <div class="text-danger mb-2"><i class="fas fa-sign-out-alt fa-lg"></i></div>
                            <small class="fw-semibold text-dark">Logout</small>
                        </button>
                    </form>
                </div>

                @if ($membership && in_array($membership->status, ['approved', 'suspended', 'expired']))
                    <div class="col-sm-6 col-md-4">
                        <a href="{{ route('member.resign.create') }}"
                           class="card text-center border text-decoration-none h-100 p-3 stat-card">
                            <div class="stat-icon mx-auto mb-2" style="background: linear-gradient(135deg, #6b7280, #4b5563); box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <small class="fw-semibold">Submit Resignation</small>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection