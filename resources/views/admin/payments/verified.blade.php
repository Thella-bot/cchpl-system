{{-- Legacy view — the primary view is admin/payment-admin/verified.blade.php --}}
@extends('layouts.admin')

@section('title', 'Verified Payments')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Verified Payments</h1>
    <p class="text-muted mb-0">Payments that have been verified and approved.</p>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    This view redirects to the main
    <a href="{{ route('admin.payments.verified') }}" class="alert-link">Verified Payments</a> page.
</div>
@endsection
