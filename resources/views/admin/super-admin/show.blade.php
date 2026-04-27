@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('admin.admins.list') }}" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left"></i> Back to Admin List
        </a>
        <h2 class="h4 mt-2 mb-0 text-gray-800">Manage Admin: <span class="fw-bold">{{ $user->name }}</span></h2>
    </div>

    <div class="row">
        <!-- Left Column: Details & Deactivation -->
        <div class="col-lg-4">
            <!-- Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-id-card text-muted me-2"></i>Admin Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong><br>{{ $user->email }}</p>
                    <p><strong>Phone:</strong><br>{{ $user->phone ?? 'N/A' }}</p>
                    <p><strong>Joined:</strong><br>{{ $user->created_at->format('d M Y') }}</p>
                    <p class="mb-0"><strong>Last Login:</strong><br>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                </div>
            </div>

            <!-- Deactivation Card -->
            @if(Auth::id() !== $user->id)
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                </div>
                <div class="card-body">
                    <p class="small">Deactivating an admin removes all their roles and prevents them from accessing any admin panels.</p>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateAdminModal">
                        Deactivate Admin
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Role Management -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-shield text-muted me-2"></i>Manage Roles</h5>
                </div>
                <form action="{{ route('admin.admins.roles.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <p class="text-muted small">Select the roles this user should have. The Super Admin role cannot be removed from the primary super admin (ID: 1).</p>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}"
                                            {{ $user->roles->contains($role) ? 'checked' : '' }}
                                            {{ $role->name === 'super_admin' && $user->id === 1 ? 'disabled' : '' }}>
                                        @if($role->name === 'super_admin' && $user->id === 1)
                                            <input type="hidden" name="roles[]" value="{{ $role->id }}">
                                        @endif
                                        <label class="form-check-label" for="role-{{ $role->id }}">
                                            {{ $role->display_name }}
                                        </label>
                                        <div class="form-text text-muted small ps-2">{{ $role->description }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-light text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Roles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Deactivation Modal -->
<div class="modal fade" id="deactivateAdminModal" tabindex="-1" aria-labelledby="deactivateAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.admins.deactivate', $user) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deactivateAdminModalLabel">Confirm Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-danger fw-bold">This action will immediately revoke all their admin privileges. This can be reversed later by a Super Admin.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Deactivate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection