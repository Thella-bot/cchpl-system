{{-- 
    Legacy view — the primary pending applications view is 
    admin/membership-admin/index.blade.php (used by MembershipAdminController@index).
    This view is kept for backward compatibility.
--}}
@extends('layouts.admin')

@section('title', 'Pending Membership Applications')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Pending Applications</h1>
    <p class="text-muted mb-0">Review and approve/reject membership applications.</p>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-1"></i>
    This view redirects to the main
    <a href="{{ route('admin.memberships.index') }}" class="alert-link">Pending Applications</a> page.
</div>
@endsection
