{{-- Legacy view — the primary view is admin/payment-admin/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Pending Payments')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Pending Payment Verifications</h1>
    <p class="text-muted mb-0">Review and verify member payment proofs.</p>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    This view redirects to the main
    <a href="{{ route('admin.payments.index') }}" class="alert-link">Pending Payments</a> page.
</div>
@endsection
