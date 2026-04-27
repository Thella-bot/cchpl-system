{{-- Legacy view — the primary view is admin/payment-admin/rejected.blade.php --}}
@extends('layouts.admin')

@section('title', 'Rejected Payments')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Rejected Payments</h1>
    <p class="text-muted mb-0">Payments that were rejected and require re-submission.</p>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    This view redirects to the main
    <a href="{{ route('admin.payments.rejected') }}" class="alert-link">Rejected Payments</a> page.
</div>
@endsection
